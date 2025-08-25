<?php //declare(strict_types=1);
//
// namespace App\Http\Controllers\Admin;
//
// use App\Http\Controllers\Controller;
// use Carbon\Carbon;
// use Core\Domains\Account\AccountLocator;
// use Core\Domains\Account\Models\AccountSearcher;
// use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
// use Core\Domains\Billing\Invoice\InvoiceLocator;
// use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
// use Core\Domains\Billing\Payment\Models\PaymentSearcher;
// use Core\Domains\Billing\Payment\PaymentLocator;
// use Core\Domains\Billing\Period\Models\PeriodSearcher;
// use Core\Domains\Billing\Period\PeriodLocator;
// use Core\Domains\Counter\CounterLocator;
// use Core\Domains\Counter\Models\CounterHistorySearcher;
// use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
// use Core\Domains\Infra\ExData\ExDataLocator;
// use Core\Domains\User\UserLocator;
// use Core\Helpers\DateTime\DateTimeHelper;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use Illuminate\Support\Facades\Log;
// use Core\Helpers\Phone\PhoneHelper;
//
// class ReestrController extends Controller
// {
//     public function read(Request $request)
//     {
//         // abort(404);
//         try {
//             $filePath = storage_path('tmp/invoice.xls');
//
//             if ( ! file_exists($filePath)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Файл не найден',
//                 ], 404);
//             }
//
//             $spreadsheet = IOFactory::load($filePath);
//             $sheets      = $spreadsheet->getAllSheets();
//
//             $result = [];
//             try {
//                 foreach ($sheets as $sheetIndex => $sheet) {
//                     // if ($sheetIndex !== 1) {
//                     //     continue;
//                     // }
//                     $sheetName = $sheet->getTitle();
//                     $sheetData = [];
//
//                     foreach ($sheet->getRowIterator() as $i => $row) {
//                         if ($i <= 8) {
//                             continue;
//                         }
//                         $rowData = [];
//                         foreach ($row->getCellIterator() as $cell) {
//                             $rowData[] = $cell->getCalculatedValue();
//                         }
//
//                         try {
//                             $accountNumber = Str::replace('-', '', $rowData[1]) . '/' . ($sheetIndex + 1);
//                             $accountSize   = $rowData[2];
//
//                             $account = AccountLocator::AccountService()->search(AccountSearcher::make()->setNumber($accountNumber))->getItems()->first();
//                             if ( ! $account) {
//                                 $account = AccountLocator::AccountFactory()->makeDefault()
//                                     ->setSize((int) $accountSize)
//                                     ->setNumber($accountNumber)
//                                 ;
//                                 $account = AccountLocator::AccountService()->save($account);
//                             }
//                             if ( ! $account?->getId()) {
//                                 continue;
//                             }
//
//                             $period = PeriodLocator::PeriodService()->search(PeriodSearcher::make())->getItems()->first();
//
//                             $invoice = InvoiceLocator::InvoiceService()
//                                 ->search(InvoiceSearcher::make()->setType(InvoiceTypeEnum::REGULAR)
//                                     ->setPeriodId($period->getId())
//                                     ->setAccountId($account->getId()),
//                                 )
//                                 ->getItems()
//                                 ->first()
//                             ;
//
//                             if ( ! $invoice) {
//                                 continue;
//                             }
//
//                             $payed = (float) $rowData[8] + (float) $rowData[9] + (float) $rowData[10];
//                             if ($payed <= 0) {
//                                 continue;
//                             }
//
//                             $payment = PaymentLocator::PaymentService()
//                                 ->search(
//                                     PaymentSearcher::make()->setInvoiceId($invoice->getId()),
//                                 )
//                                 ->getItems()
//                                 ->first()
//                             ;
//
//                             if ( ! $payment) {
//                                 $payment = PaymentLocator::PaymentFactory()->makeDefault();
//                             }
//
//                             $payment
//                                 ->setAccountId($account->getId())
//                                 ->setInvoiceId($invoice->getId())
//                                 ->setModerated(true)
//                                 ->setVerified(true)
//                                 ->setCost($payed)
//                             ;
//
//                             $payment = PaymentLocator::PaymentService()->save($payment);
//
//                         }
//                         catch (\Exception $e) {
//                             $a = $e;
//                         }
//                         $sheetData[] = $rowData;
//                     }
//
//                     $result[$sheetName] = $sheetData;
//                 }
//             }
//             catch (\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//
//             return response()->json([
//                 'success' => true,
//                 'data'    => $result,
//             ]);
//
//         }
//         catch (\Exception $e) {
//             Log::error('Ошибка при чтении файла reestr.xls', [
//                 'message' => $e->getMessage(),
//                 'trace'   => $e->getTraceAsString(),
//             ]);
//
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Ошибка при чтении файла: ' . $e->getMessage(),
//             ], 500);
//         }
//     }
//
//     public function read1(Request $request)
//     {
//         try {
//             $filePath = storage_path('tmp/reestr.xls');
//
//             if ( ! file_exists($filePath)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Файл не найден',
//                 ], 404);
//             }
//
//             $spreadsheet = IOFactory::load($filePath);
//             $sheets      = $spreadsheet->getAllSheets();
//
//             $result = [];
//             try {
//                 foreach ($sheets as $sheetIndex => $sheet) {
//                     // if ($sheetIndex !== 2) {
//                     //     continue;
//                     // }
//                     $sheetName = $sheet->getTitle();
//                     $sheetData = [];
//
//                     foreach ($sheet->getRowIterator() as $i => $row) {
//                         if ($i <= 5) {
//                             continue;
//                         }
//                         $rowData = [];
//                         foreach ($row->getCellIterator() as $cell) {
//                             $rowData[] = $cell->getCalculatedValue();
//                         }
//
//                         try {
//                             $accountNumber  = Str::replace('-', '', $rowData[5]);
//                             $cadastrNumber  = $rowData[6];
//                             $accountSize    = $rowData[7];
//                             $cadastrRegDate = DateTimeHelper::toCarbonOrNull($rowData[8]);
//
//                             $accountSearcher = AccountSearcher::make()->setNumber($accountNumber);
//                             $account         = AccountLocator::AccountService()->search($accountSearcher)->getItems()->first();
//                             if ( ! $account) {
//                                 $account = AccountLocator::AccountFactory()->makeDefault();
//                             }
//
//                             if ( ! $account?->getId()) {
//                                 continue;
//                             }
//                             $account->setSize((int) $accountSize)
//                                 ->getExData()
//                                 ->setCadastreNumber($cadastrNumber)
//                                 ->setRegistryDate($cadastrRegDate)
//                             ;
//                             $account = AccountLocator::AccountService()->save($account);
//
//                             $accountExData = ExDataLocator::ExDataService()->getByTypeAndReferenceId(ExDataTypeEnum::ACCOUNT, $account->getId());
//                             $accountExData->setData(
//                                 $account->getExData()
//                                     ->setCadastreNumber($cadastrNumber)
//                                     ->setRegistryDate($cadastrRegDate)
//                                     ->jsonSerialize(),
//                             );
//                             $accountExData = ExDataLocator::ExDataService()->save($accountExData);
//
//                             // Разбираем строку с протоколом
//                             if (isset($rowData[2]) && str_contains($rowData[2], 'принят в члены Товарищества')) {
//                                 $protocolData = $this->parseProtocolString($rowData[2]);
//                                 $rowData[2]   = $protocolData;
//                             }
//
//                             // Извлекаем и нормализуем первый номер телефона
//                             if (isset($rowData[3])) {
//                                 $phone      = PhoneHelper::normalizePhone(PhoneHelper::extractAndNormalizePhone($rowData[3]));
//                                 $rowData[3] = $phone;
//                             }
//
//                             [$lastName, $firstName, $middleName] = [null, null, null];
//                             try {
//                                 [$lastName, $firstName, $middleName] = explode(' ', $rowData[1]);
//                             }
//                             catch (\Exception $e) {
//                                 $lastName = explode(' ', $rowData[1], 2)[0];
//                             }
//                             $email = $rowData[4];
//                             if ( ! $email) {
//                                 $email = Str::slug($lastName) . '.' . Str::slug($firstName) . '@' . Str::slug($middleName) . '.ru';
//                             }
//
//                             $user = UserLocator::UserService()->getByEmail($email);
//                             if ( ! $user) {
//                                 $user = UserLocator::UserFactory()->makeDefault()
//                                     ->setEmail($email)
//                                     ->setEmailVerifiedAt(Carbon::now())
//                                     ->setPassword(Str::random(10))
//                                 ;
//                             }
//                             $user
//                                 ->setLastName($lastName)
//                                 ->setFirstName($firstName)
//                                 ->setMiddleName($middleName)
//                                 ->setPhone($phone)
//                                 ->setAccount($account)
//                             ;
//                             $user = UserLocator::UserService()->save($user);
//
//                             $userExData    = ExDataLocator::ExDataService()->getByTypeAndReferenceId(ExDataTypeEnum::USER, $user->getId());
//                             $membershipDate = Str::replace('москва', '', Str::lower($protocolData['date'] ?? ''));
//                             $userExData->setData(
//                                 $user->getExData()
//                                     ->setMembershipDate(DateTimeHelper::toCarbonOrNull($membershipDate))
//                                     ->setMembershipDutyInfo($protocolData['protocol'] ?? null)
//                                     ->jsonSerialize(),
//                             );
//                             $userExData = ExDataLocator::ExDataService()->save($userExData);
//                             $b          = 1;
//                             // $counterDate1  = DateTimeHelper::toCarbonOrNull($rowData[9]);
//                             // $counterValue1 = $rowData[10];
//                             // $counterDate2  = DateTimeHelper::toCarbonOrNull($rowData[11]);
//                             // $counterValue2 = $rowData[12];
//                             //
//                             // $counters = CounterLocator::CounterService()->getByAccountId($account->getId());
//                             // $counter1 = $counters->first();
//                             // $counter2 = $counters->last();
//                             //
//                             // if ($counterDate1 && $counterValue1) {
//                             //     if ( ! $counter1) {
//                             //         $counter1 = CounterLocator::CounterFactory()->makeDefault()
//                             //             ->setAccountId($account->getId())
//                             //             ->setNumber($account->getNumber() . '-' . 1)
//                             //         ;
//                             //         CounterLocator::CounterService()->save($counter1);
//                             //     }
//                             //
//                             //     $counterHistory1 = CounterLocator::CounterHistoryService()->search(
//                             //         CounterHistorySearcher::make()
//                             //             ->setCounterId($counter1->getId())
//                             //             ->defaultSort(),
//                             //     )->getItems()->first();
//                             //
//                             //     if (
//                             //         ! $counterHistory1?->getId() ||
//                             //         $counterHistory1->getValue() !== $counterValue1
//                             //
//                             //     ) {
//                             //         $counterHistory1 = CounterLocator::CounterHistoryFactory()->makeDefault()
//                             //             ->setCounterId($counter1->getId())
//                             //             ->setDate($counterDate1)
//                             //             ->setValue($counterValue1)
//                             //             ->setIsVerified(true)
//                             //         ;
//                             //         CounterLocator::CounterHistoryService()->save($counterHistory1);
//                             //     }
//                             // }
//                             //
//                             // if ($counterDate2 && $counterValue2) {
//                             //     if ( ! $counter2) {
//                             //         $counter2 = CounterLocator::CounterFactory()->makeDefault()
//                             //             ->setAccountId($account->getId())
//                             //             ->setNumber($account->getNumber() . '-' . 2)
//                             //         ;
//                             //         CounterLocator::CounterService()->save($counter2);
//                             //     }
//                             //
//                             //     $counterHistory2 = CounterLocator::CounterHistoryService()->search(
//                             //         CounterHistorySearcher::make()
//                             //             ->setCounterId($counter2->getId())
//                             //             ->defaultSort(),
//                             //     )->getItems()->first();
//                             //
//                             //     if (
//                             //         ! $counterHistory2?->getId() ||
//                             //         $counterHistory2->getValue() !== $counterValue2
//                             //
//                             //     ) {
//                             //         $counterHistory2 = CounterLocator::CounterHistoryFactory()->makeDefault()
//                             //             ->setCounterId($counter2->getId())
//                             //             ->setDate($counterDate2)
//                             //             ->setValue($counterValue2)
//                             //             ->setIsVerified(true)
//                             //         ;
//                             //         CounterLocator::CounterHistoryService()->save($counterHistory2);
//                             //     }
//                             // }
//                         }
//                         catch (\Exception $e) {
//                             $a = $e;
//                         }
//                         $sheetData[] = $rowData;
//                     }
//
//                     $result[$sheetName] = $sheetData;
//                 }
//             }
//             catch (\Exception $e) {
//                 Log::error($e->getMessage());
//             }
//
//             return response()->json([
//                 'success' => true,
//                 'data'    => $result,
//             ]);
//
//         }
//         catch (\Exception $e) {
//             Log::error('Ошибка при чтении файла reestr.xls', [
//                 'message' => $e->getMessage(),
//                 'trace'   => $e->getTraceAsString(),
//             ]);
//
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Ошибка при чтении файла: ' . $e->getMessage(),
//             ], 500);
//         }
//     }
//
//     private function parseProtocolString(string $string): array
//     {
//         // Ищем номер протокола
//         preg_match('/Протокол № (\d+)/', $string, $protocolMatches);
//         $protocolNumber = $protocolMatches[1] ?? null;
//
//         // Ищем дату
//         preg_match('/от (\d{2}\.\d{2}\.\d{4})/', $string, $dateMatches);
//         $date = $dateMatches[1] ?? null;
//
//         return [
//             'protocol' => $protocolNumber ? "Протокол № $protocolNumber" : null,
//             'date'     => $date,
//         ];
//     }
// }