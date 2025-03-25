<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ExpenseModel;
use App\Models\ExpenseShareModel;
use App\Models\ExpenseTransferModel;
use App\Models\GuestListModel;
use App\Models\TripDetails;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;



class ExpenseController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/add-expense",
     *     summary="Add expense",
     *     description="Add expense",
     *     tags={"Expense"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"trip_id", "paid_by", "amount", "name", "description", "expense_on", "type", "shareList"},
     *             @OA\Property(property="trip_id", type="integer", example=1),
     *             @OA\Property(property="paid_by", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", example=100),
     *             @OA\Property(property="name", type="string", example="Flight ticket"),
     *             @OA\Property(property="description", type="string", example="Flight from Delhi to Mumbai"),
     *             @OA\Property(property="expense_on", type="string", example="2023-02-25"),
     *             @OA\Property(property="type", type="string", example="Expense", enum={"Expense", "Deposit"}),
     *             @OA\Property(property="shareList", type="array", example={{
     *                 "debtor": 1,
     *                 "amount": 100
     *             }, {
     *                 "debtor": 2,
     *                 "amount": 50
     *             }})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function addExpense(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'trip_id' => 'required',
            'paid_by' => 'required',
            'amount' => 'required',
            'name' => 'required',
            'description' => 'required',
            'expense_on' => 'required',
            'type' => 'required|in:Expense,Deposit',
            'shareList' => 'required|array',
        ];
        $errorMessages = [
            'trip_id.required' => 'Trip id is required',
            'paid_by.required' => 'Paid by is required',
            'amount.required' => 'Amount is required',
            'name.required' => 'Name is required',
            'description.required' => 'Description is required',
            'expense_on.required' => 'Expense on is required',
            'type.required' => 'Type is required',
            'shareList.required' => 'Expense Share list is required',
            'shareList.array' => 'Expense Share list must be in array form',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        // Get the user ID from the authentication token using your helper function
        $created_by = Helpers::getUserIdFromToken($authToken);

        DB::beginTransaction();
        try {
            $amount = $json['amount'];
            $deposit = 0;
            if ($json['type'] == 'Deposit') {
                $amount = $json['amount'] * count($json['shareList']);
                $deposit = $json['amount'];
            }
            $expenseData = [
                'trip_id' => $json['trip_id'],
                'guest_id' => $json['paid_by'],
                'amount' => $amount,
                'name' => $json['name'],
                'description' => $json['description'],
                'expense_on' => $json['expense_on'],
                'type' => $json['type'],
                'created_by' => $created_by,
                'deposit_amount' => $deposit,
            ];
            $expenseId = ExpenseModel::create($expenseData)->id;

            //add share
            if (!$this->addExpenseShare($json['shareList'], $expenseId, $json['trip_id'], $json['paid_by'], $expenseData)) {
                $this->failExpenseCreation();
            }
            DB::commit();
            return Helpers::success('Add expense successfully', json_decode('{}'), $authToken);
        } catch (\Exception $e) {
            $this->failExpenseCreation();
        }
    }



    /**
     * @param array $shareArray
     * @param int $expenseId
     * @param int $tripId
     * @param int $creditor
     * @param array $expenseData
     *
     * @return bool
     * @throws \Exception
     */

    public function addExpenseShare($shareArray, $expenseId, $tripId, $creditor, $expenseData)
    {
        DB::beginTransaction();
        try {
            foreach ($shareArray as $share) {
                if ($creditor == $share['debtor']) {
                    $shareData = [
                        'expense_id' => $expenseId,
                        'trip_id' => $tripId,
                        'amount' => $share['amount'],
                        'creditor' => $creditor,
                        'debtor' => $share['debtor'],
                        'paid_amount' => $share['amount'],
                    ];
                    ExpenseShareModel::create($shareData);
                } else {
                    $previous = ExpenseShareModel::where('creditor', $share['debtor'])
                        ->where('debtor', $creditor)
                        ->where('trip_id', $tripId)
                        ->where('amount', '!=', 'paid_amount')
                        ->get();
                    $totalAmount = $share['amount'];
                    foreach ($previous as $item) {
                        if ($totalAmount > 0) {
                            $payableAmount = $item->amount - $item->paid_amount;
                            if ($payableAmount > 0) {
                                $paidAmount = $payableAmount;
                                if ($totalAmount <= $payableAmount) {
                                    $paidAmount = $totalAmount;
                                }
                                $totalAmount = $totalAmount - $paidAmount;
                                $item->paid_amount = $item->paid_amount + $paidAmount;
                                $item->paid_on = now();
                                $item->paid_by = $creditor;
                                $item->save();
                            }
                        }
                    }
                    $shareData = [
                        'expense_id' => $expenseId,
                        'trip_id' => $tripId,
                        'amount' => $share['amount'],
                        'paid_amount' => $share['amount'] - $totalAmount,
                        'creditor' => $creditor,
                        'debtor' => $share['debtor'],
                    ];
                    ExpenseShareModel::create($shareData);
                }
            }
            DB::commit();
            try {
                $guestDetail = GuestListModel::select('email_id', 'first_name', 'last_name')
                    ->where('trip_id', $tripId)
                    ->where(function ($query) use ($creditor) {
                        $query
                            ->where('role', 'Host')
                            ->orWhere('is_co_host', 1)
                            //->orWhere('role', 'VIP')
                            ->orWhere('id', $creditor);
                    })
                    ->get();

                $tripName = TripDetails::select('trip_name')
                    ->where('id', $tripId)
                    ->first();

                // Assuming $expenseData is an associative array
                // $expenseData['first_name'] = $guestDetail['first_name'];
                // $expenseData['last_name'] = $guestDetail['last_name'];
                $expenseData['trip_name'] = $tripName['trip_name'];

                $recipients = $guestDetail->pluck('email_id')->toArray();
                $template = 'addExpense';
                $subject = ' Trip - ' . $tripName['trip_name'] . '- Expense Added';
                $sendMail = Helpers::sendEmail($recipients, $expenseData, $template, $subject);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    /**
     * Revert the database to its previous state when an error occurs while adding an expense.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failExpenseCreation()
    {
        DB::rollback();
        return Helpers::error('Something went wrong in add expense, please try again', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/get-activities",
     *     summary="Get activities",
     *     description="Get activities",
     *     tags={"Expense"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"trip_id"},
     *             @OA\Property(property="trip_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function getActivities(Request $request)
    {
        /**
         * for first line, <expense_on> - <Description>
         * if u_id == your id then your are creditor, show "you paid $<totalAmount>" else "<creditor> paid $<totalAmount>
         * if u_id == your id
         *  {
         *      if remainAmount > 0 then "You lent <remainAmount>" else "You Received<amount>"
         *  }else{
         *      if remainAmount > 0 then "You borrowed <remainAmount>" else "You Paid<amount>"
         *  }
         *
         */

        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'trip_id' => 'required',
        ];
        $errorMessages = [
            'trip_id.required' => 'Trip id is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        // Get the user ID from the authentication token using your helper function
        $userId = Helpers::getUserIdFromToken($authToken);
        //find guest id
        $guestId = GuestListModel::where('trip_id', $json['trip_id'])
            ->where('u_id', $userId)
            ->where('is_deleted', 0)
            ->first()->id;
        if ($guestId) {
            $data = DB::select(
                "Select * from (
                Select e.amount as totalAmount,e.guest_id,c.u_id,CONCAT(c.first_name,' ',c.last_name) as creditor,e.name,e.description,e.expense_on,e.type,
                       sum(case when s.creditor = " .
                    $guestId .
                    ' then (s.amount-s.paid_amount) else (case when s.debtor = ' .
                    $guestId .
                    " then (s.amount-s.paid_amount) else 0 end) end) as remainAmount,
                       sum(case when s.creditor = " .
                    $guestId .
                    ' then (s.amount) else (case when s.debtor = ' .
                    $guestId .
                    " then (s.amount) else 0 end) end) as amount,s.expense_id
                from tbl_expense_share as s inner join tbl_expense as e on s.expense_id = e.id
                    inner join trip_guests as c on e.guest_id = c.id
                    where s.trip_id = " .
                    $json['trip_id'] .
                    "
                group by s.expense_id
                ) as x where x.amount > 0 order by expense_id desc",
            );
            return Helpers::success('activities listed successfully', $data, $authToken);
        } else {
            return Helpers::error('Something went wrong in fetching activities, please try again', 200);
        }
    }

    /**
     * Retrieves and returns resolution details for a specified trip.
     *
     * This function fetches the financial resolutions for the current user
     * in the context of a specific trip. It calculates the amount owed or
     * owed by others and retrieves the opponent's details, including their
     * payment usernames. The function processes the request, validates the
     * input and returns the resolution data. If the amount is negative, it
     * indicates that the user owes the opponent, otherwise the opponent owes
     * the user.
     *
     * @param Request $request The HTTP request object containing the trip ID.
     * @return \Illuminate\Http\JsonResponse A JSON response with resolution details.
     */

    public function getResolutions(Request $request)
    {
        /**
         * if(amount < 0){
         *    "You owe <opponent> $<amount>" and pay button
         * }else{
         *    "<opponent> owes you $<amount>
         * }
         */
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'trip_id' => 'required',
        ];
        $errorMessages = [
            'trip_id.required' => 'Trip id is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        // Get the user ID from the authentication token using your helper function
        $userId = Helpers::getUserIdFromToken($authToken);
        //find guest id
        $guestId = GuestListModel::where('trip_id', $json['trip_id'])
            ->where('u_id', $userId)
            ->where('is_deleted', 0)
            ->first()->id;
        if ($guestId) {
            $data = DB::select(
                "Select ROUND(x.amount,2) as amount,x.opp,concat(g.first_name,' ',g.last_name) as opponent,u.paypal_username,u.venmo_username from (
                Select sum(case when s.creditor = " .
                    $guestId .
                    " then (s.amount-s.paid_amount) else -(s.amount-s.paid_amount) end) as amount,
                       s.creditor,s.debtor, (case when s.creditor = " .
                    $guestId .
                    " then s.debtor else s.creditor end) as opp
                from tbl_expense_share as s where (s.creditor = " .
                    $guestId .
                    ' or s.debtor = ' .
                    $guestId .
                    ') and s.creditor != s.debtor and s.trip_id = ' .
                    $json['trip_id'] .
                    "
                group by LEAST(s.creditor, s.debtor),GREATEST(s.creditor, s.debtor)
                ) x join trip_guests as g on x.opp = g.id left join tbl_users u on g.u_id = u.id
                where x.amount != 0",
            );
            return Helpers::success('resolution listed successfully', $data, $authToken);
        } else {
            return Helpers::error('Something went wrong in fetching activities, please try again', 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/pay-expense",
     *     summary="Pay expense",
     *     description="Pay expense",
     *     tags={"Expense"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"trip_id", "creditor", "amount", "paid_by"},
     *             @OA\Property(property="trip_id", type="integer", example=1),
     *             @OA\Property(property="creditor", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", example=100),
     *             @OA\Property(property="paid_by", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function payExpense(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'trip_id' => 'required',
            'creditor' => 'required',
            'amount' => 'required',
            'paid_by' => 'required',
        ];
        $errorMessages = [
            'trip_id.required' => 'Trip id is required',
            'creditor.required' => 'Creditor is required',
            'amount.required' => 'Amount is required',
            'paid_by.required' => 'Paid by is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        // Get the user ID from the authentication token using your helper function
        $userId = Helpers::getUserIdFromToken($authToken);
        //find guest id
        $guestId = GuestListModel::where('trip_id', $json['trip_id'])
            ->where('u_id', $userId)
            ->where('is_deleted', 0)
            ->first()->id;

        $expenseShares = ExpenseShareModel::where('creditor', $json['creditor'])
            ->where('debtor', $guestId)
            ->where('amount', '!=', 'paid_amount')
            ->where('trip_id', $json['trip_id'])
            ->get();

        DB::beginTransaction();
        try {
            $totalAmount = $json['amount'];
            foreach ($expenseShares as $item) {
                if ($totalAmount > 0) {
                    $payableAmount = $item->amount - $item->paid_amount;
                    if ($payableAmount > 0) {
                        $paidAmount = $payableAmount;
                        if ($totalAmount <= $payableAmount) {
                            $paidAmount = $totalAmount;
                        }
                        $totalAmount = $totalAmount - $paidAmount;
                        $item->paid_amount = $item->paid_amount + $paidAmount;
                        $item->paid_on = now();
                        $item->paid_by = $json['paid_by'];
                        $item->save();
                    }
                }
            }
            ExpenseTransferModel::create([
                "sender" => $guestId,
                "receiver" => $json['creditor'],
                "amount" => $json['amount'],
                "trip_id" => $json['trip_id'],
                "paid_by" => $json['paid_by']
            ]);
            DB::commit();
            return Helpers::success('Pay expense done successfully', json_decode('{}'), $authToken);
        } catch (\Exception $e) {
            DB::rollback();
            return Helpers::error('Something went wrong in paying expense, please try again', 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/exp-report",
     *     summary="Expense report",
     *     description="Expense report",
     *     tags={"Expense"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"trip_id"},
     *             @OA\Property(property="trip_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function expReport(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'trip_id' => 'required',
        ];
        $errorMessages = [
            'trip_id.required' => 'Trip id is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        $guests = GuestListModel::select('trip_guests.id', DB::Raw("Concat(first_name,' ',last_name) as name"), 'trip_guests.email_id')
            ->join('tbl_expense_share', 'trip_guests.id', 'tbl_expense_share.debtor')
            ->where('trip_guests.trip_id', $json['trip_id'])
            ->where('invite_status', '!=', 'Declined')
            ->where('trip_guests.is_deleted', 0)
            ->groupBy('tbl_expense_share.debtor')
            ->get();
        $query = ExpenseModel::query();
        $query->select('tbl_expense.id', 'tbl_expense.amount', 'tbl_expense.name', DB::Raw("DATE_FORMAT(tbl_expense.expense_on,'%d %b %y') as 'expenseOn'"), 'tbl_expense.type', 'trip_guests.first_name', 'trip_guests.last_name');
        $query->join('trip_guests', 'trip_guests.id', 'tbl_expense.guest_id');
        foreach ($guests as $item) {
            $query->addSelect(Db::Raw("(Select amount from tbl_expense_share where expense_id = tbl_expense.id and debtor = " . $item->id . ") as '" . $item->name . "'"));
        }
        $query->where('tbl_expense.trip_id', $json['trip_id']);
        $costs = $query->get();

        $resolution = DB::Select(
            "select (sum(amount)-sum(paid_amount)) as amount,Concat(tg.first_name,' ', tg.last_name) as Creditor,Concat(debt.first_name,' ', debt.last_name) as Debtor from tbl_expense_share
            join trip_guests tg on tbl_expense_share.creditor = tg.id join trip_guests debt on tbl_expense_share.debtor = debt.id
            where tbl_expense_share.trip_id = " . $json['trip_id'] . " and paid_amount != amount group by creditor,debtor"
        );

        $transfers = DB::select(
            "select amount,Concat(tg.first_name,' ', tg.last_name) as Sender,Concat(debt.first_name,' ', debt.last_name) as Receiver,DATE_FORMAT(tbl_expense_transfer.created_at,'%d %b %y') as TransferOn from tbl_expense_transfer
            join trip_guests tg on tbl_expense_transfer.sender = tg.id join trip_guests debt on tbl_expense_transfer.receiver = debt.id
            where tbl_expense_transfer.trip_id = " . $json['trip_id'] . ""
        );



        $guestEmails = $guests->pluck('email_id')->toArray();
        $data["email"] = $guestEmails;


        $data["title"] = "Expense Report";
        $data["cost"] = $costs;
        $data["guest"] = $guests;
        $data["resolution"] = $resolution;
        $data['transfer'] = $transfers;

        $pdf = PDF::loadView('emails.report', $data);

        if ($data["email"]) {
            try {
                Mail::send('emails.report', $data, function ($message) use ($data, $pdf) {
                    $message->to($data["email"])
                        ->subject($data["title"])
                        ->attachData($pdf->output(), "text.pdf");
                });
                return Helpers::success('Email sent successfully', ["guest" => $guests, "costs" => $costs, "resolution" => $resolution, "transfer" => $transfers], $authToken);
            } catch (\Exception $e) {
                // Email sending failed
                return Helpers::error('Email sending failed ');
            }
        } else {
            return Helpers::error('Email sending failed ');
        }
    }
}
