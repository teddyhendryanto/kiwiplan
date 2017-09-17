<?php

namespace App\Http\Controllers\RollStock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;

use Auth;
use Carbon\Carbon;
use DB;
use File;
use Storage;

use App\Models\ReceiveRoll;
use App\Models\VerifyRoll;
use App\Models\EdiExport;
use App\Models\EdiExportDetail;
use App\Models\EdiExportHistory;

class EdiExportController extends Controller
{
    use GeneralTrait;

    public function getLastEDICounter(){
      $lastcounter = EdiExport::select(DB::raw('ifnull(max(counter),0) as counter'))
      ->where('yyyy',date('Y'))
      ->first();

      $newcounter = $lastcounter->counter+1;

      return $newcounter;
    }

    public function export_edi_falied($exec_type, $status_remark, $todays_log){
      $log = date('Y-m-d H:i:s').' >> '.$status_remark.' >> '.$exec_type;
      Storage::append('log/'.$todays_log, $log);
      echo "Data Not Found.";
      return;
    }

    public function export_edi($exec_type, $results){
      $todays_log = 'log_'.date('Ymd').'.txt';

      if($results->isEmpty()){
        $status_remark = 'DATA NOT FOUND';
        $this->export_edi_falied($exec_type, $status_remark, $todays_log);
        return;
      }

      $counter = $this->getLastEDICounter();
      $newcounter = sprintf('%05d', $counter);
      $exp_filename = $newcounter.'.txt';
      $roifilename = 'roi'.$newcounter.'.edi';
      $rrifilename = 'rri'.$newcounter.'.edi';

      $exp = '';
      $roi = '';
      $rri = '';
      $export_details = array();
      foreach ($results as $row) {
        $id = $row->id;
        $site_id = $row->receive_roll->site_id;
        $site = $row->receive_roll->site->short_name;
        $po_id = $row->receive_roll->po_id;
        $po_num = $row->receive_roll->po_num;
        $po_date = $row->receive_roll->purchase_order->po_date;
        $receive_date = $row->receive_roll->receive_date;
        $receive_time = $row->receive_roll->receive_time;
        $supplier_id = $row->receive_roll->supplier_id;
        $supplier = $row->receive_roll->supplier->short_name;
        $paper_key = $row->receive_roll->paper_key;
        $paper_width = $row->receive_roll->paper_width;
        $paper_price = $row->receive_roll->paper_price;
        $supplier_roll_id = $row->receive_roll->supplier_roll_id;
        $unique_roll_id = $row->receive_roll->unique_roll_id;
        $roll_weight = $row->receive_roll->roll_weight;
        $roll_diameter = $row->receive_roll->roll_diameter;
        $doc_ref = $row->receive_roll->doc_ref;
        $wagon = $row->receive_roll->wagon;
        $created_at = $row->receive_roll->created_at;
        $updated_at = $row->receive_roll->updated_at;
        $rstatus = $row->receive_roll->rstatus;
        $currency = $row->receive_roll->supplier->currency;
        $selling_rate = $row->receive_roll->selling_rate;
        $rss_store = $row->receive_roll->rss_store;
        $rss_loc = $row->receive_roll->rss_loc;
        $remarks = $row->receive_roll->remarks;
        $lead_time = $row->receive_roll->supplier->lead_time;

        $kwTranDate = date('ymd',strtotime($receive_date));
        $kwTranTime = date('His',strtotime("-1 minute", strtotime($receive_time)));
        $kwSupNo = $supplier_id;
        $kwOrdNo = $po_num;
        $kwPaperKey = $paper_key;
        $kwPaperWidth = $paper_width;
        $kwCurrency = 'IDR'; // Always IDR because Indonesia should received in Rupiah.
        $kwCurrencyflag = $currency; // real currency from paper_supplier table
        if($kwCurrencyflag != "IDR"){
          $kwPaperPrice = (int)(($paper_price/1000) * $selling_rate);
        }
        else{
          $kwPaperPrice = (int)$paper_price;
        }
        $kwStore = $rss_store;
        $kwLoc = $rss_loc;
        $kwSupRoll = $supplier_roll_id;
        if(strlen($supplier_roll_id)>20){
          $kwSupRoll = str_replace('.','',$supplier_roll_id);
        }
        $kwUniqRoll = $unique_roll_id;
        $kwWgtRcv = (int)$roll_weight;
        $kwLenRcv = '';
        $kwDiaRcv = (int)$roll_diameter > 0 ? (int)$roll_diameter : '1250';
        $kwSentDate = '';
        $kwSentTime = '';
        $kwDocket = $doc_ref;
        $kwWagon = $wagon;
        $kwRcvDate = date('ymd',strtotime($receive_date));
        $kwRcvTime = date('His',strtotime($receive_time));
        $kwComment = $remarks;
        $kwExternalID = '';
        $kwTimeZone = 'WIB';
        $kwPlant = $site_id;
        $kwUOM = 'MM';
        $kwPriceBasis = 'T';
        $kwOrdTime = date('His');
        $kwDueTime = date('His');
        $kwOrdDate = (string)date('ymd',strtotime($po_date));
        $kwDueDate = date('ymd', strtotime("+".$lead_time." day", strtotime($kwOrdDate)));
        $kwRollOrd = 1;

        // EXPORTED
  			$exp.=$rstatus.' * '.$site.' * '.$po_num.' * '.$receive_date.' * '.$receive_time.' * ';
  			$exp.=$this->fixWidthWords($supplier_id,2).' * '.$this->fixWidthWords($supplier,15).' * '.$this->fixWidthWords($paper_key,7).' * '.$this->fixWidthWords($paper_width,4).' * '.$this->fixWidthWords($paper_price,6).' * ';
  			$exp.=$this->fixWidthWords(str_replace('.','',$supplier_roll_id),20).' * '.$unique_roll_id.' * '.$this->fixWidthWords($roll_weight,6).' * '.$this->fixWidthWords($roll_diameter,6).' * '.$this->fixWidthWords($doc_ref,15).' * '.$this->fixWidthWords($wagon,10).' * ';
  			$exp.=$created_at.' * '.$updated_at."\n";

        // ROI EDI FILE
        $roi.='*RSS*ADORD****'.$kwTranDate.'*'.$kwTranTime.'*'.$kwTimeZone.'*'.$kwPlant.'***'.$kwSupNo.'***'.$kwOrdNo;
        $roi.='*'.$kwPaperKey.'**'.$kwUOM.'*'.$kwPaperWidth.'*0*0*'.$kwCurrency.'*'.$kwPriceBasis.'*'.$kwPaperPrice.'*1*'.$kwStore;
        $roi.='*'.$kwOrdDate.'*'.$kwDueDate.'*'.$kwRollOrd.'*'.$kwWgtRcv.'**'.$kwExternalID;
        $roi.='****'.$kwOrdTime.'*'.$kwDueTime.'*'."\n";

        // RRI EDI FILE
        $rri.='*RSS*RVDLV****'.$kwTranDate.'*'.$kwTranTime.'*'.$kwTimeZone.'*'.$kwPlant.'***'.$kwSupNo.'***'.$kwOrdNo;
        $rri.='*'.$kwPaperKey.'**'.$kwUOM.'*'.$kwPaperWidth.'*****'.$kwStore;
        $rri.='*'.$kwLoc.'*1*'.$kwWgtRcv.'*'.$kwLenRcv.'*'.$kwSentDate.'*'.$kwSentTime;
        $rri.='*'.$kwDocket.'*'.$kwWagon.'*'.$kwRcvDate.'*'.$kwRcvTime.'*'.$kwUOM.'*'.$kwDiaRcv.'****'.$kwExternalID.'****'."\n";
        $rri.='*RSS*RVRCV****'.$kwTranDate.'*'.$kwTranTime.'*'.$kwTimeZone.'*'.$kwPlant.'***'.$kwSupNo.'***'.$kwOrdNo;
        $rri.='*'.$kwPaperKey.'**'.$kwUOM.'*'.$kwPaperWidth.'**'.$kwPriceBasis.'*'.$kwPaperPrice.'**'.$kwStore;
        $rri.='*'.$kwLoc.'*'.$kwSupRoll.'*'.$kwUniqRoll.'*'.$kwWgtRcv.'*'.$kwLenRcv.'*'.$kwSentDate.'*'.$kwSentTime;
        $rri.='*'.$kwDocket.'*'.$kwWagon.'*'.$kwRcvDate.'*'.$kwRcvTime.'*'.$kwUOM.'*'.$kwDiaRcv.'********'.$kwComment.'*'."\n";

        // after write line -> store to array
        $export_details[] = array(
          'edi_counter' => $newcounter,
          'verify_id' => $id
        );
      }
      // write file exported
      Storage::append('exported/'.$exp_filename, $exp);
      // write roi file
      Storage::append($roifilename, $roi);
      // write rri file
      Storage::append($rrifilename, $rri);

      // check if successfully write to Storage
      $exists = Storage::exists($roifilename,$rrifilename);
      if($exists){
        // save log to edi_exports;
        $export = new EdiExport;
        $export->yyyy = date('Y');
        $export->counter = $counter;
        $export->edi_counter = $newcounter;
        $export->order_file = $roifilename;
        $export->receiving_file = $rrifilename;
        $export->exec_type = $exec_type;
        $export->created_by = 'scheduler';
        $export->save();

        // save array to edi_export_details
        for ($i=0; $i < count($export_details); $i++) {
          $details = new EdiExportDetail;
          $details->edi_export_id = $export->id;
          $details->edi_counter = $export_details[$i]['edi_counter'];
          $details->verify_roll_id = $export_details[$i]['verify_id'];
          $details->created_by = 'scheduler';
          $details->save();

          // after save to export_details, update status exported to true and exported_count + 1
          $verify_roll = VerifyRoll::find($export_details[$i]['verify_id']);
          $verify_roll->exported = true;
          $verify_roll->exported_count = ($verify_roll->exported_count)+1;
          $verify_roll->save();
        }

        // success write file
        $status_remark = 'WRITE SUCCESS';
        $log = date('Y-m-d H:i:s').' >> ROI: '.$roifilename.' WRITE SUCCESS | RRI: '.$rrifilename.' WRITE SUCCESS | ROWS: '.count($results).' >> '.$exec_type;
        Storage::append('log/'.$todays_log, $log);
        // save success write to db
        $this->edi_export_history($export->id, $status_remark, 'scheduler');

        // copy to other Directory
        $full_path_src_roi = Storage::disk('local')->getDriver()->getAdapter()->applyPathPrefix($roifilename);
        $full_path_dst_roi = Storage::disk('remote')->getDriver()->getAdapter()->applyPathPrefix($roifilename);
        $full_path_src_rri = Storage::disk('local')->getDriver()->getAdapter()->applyPathPrefix($rrifilename);
        $full_path_dst_rri = Storage::disk('remote')->getDriver()->getAdapter()->applyPathPrefix($rrifilename);
        // copy roi
        File::copy($full_path_src_roi, $full_path_dst_roi);
        // copy rri
        File::copy($full_path_src_rri, $full_path_dst_rri);

        // check if file copied successfully to other drive
        $exists_remote = Storage::disk('remote')->exists($roifilename,$rrifilename);
        if($exists_remote){
          // success copy file to another directory
          $status_remark = 'MOVE SUCCESS';
          $log = date('Y-m-d H:i:s').' >> ROI: '.$roifilename.' '.$this->fixWidthWords($status_remark, 13).' | '.'RRI: '.$rrifilename.' '.$this->fixWidthWords($status_remark, 13);
          Storage::append('log/'.$todays_log, $log);
          // save success copy file to another directory to db
          $this->edi_export_history($export->id, $status_remark, 'scheduler');
        }
        else{
          // failed copy file to another directory
          $status_remark = 'MOVE FAILED';
          $log = date('Y-m-d H:i:s').' >> ROI: '.$roifilename.' '.$this->fixWidthWords($status_remark, 13).' | '.'RRI: '.$rrifilename.' '.$this->fixWidthWords($status_remark, 13);
          Storage::append('log/'.$todays_log, $log);
          // save failed copy file to another directory to db
          $this->edi_export_history($export->id, $status_remark, 'scheduler', 'DIRECTORY NOT FOUND');
        }

        echo "Task Complete";
      }
      else{
        // failed write file
        $status_remark = 'WRITE FAILED';
        $this->export_edi_falied($exec_type, $status_remark, $todays_log);
        // save failed write file to db
        $this->edi_export_history($export->id, $status_remark, 'scheduler', 'ERROR WHILE WRITE FILE');
        return;
      }

    }

    public function edi_export_history($edi_export_id, $edi_status, $created_by, $error = null){
      // save success write to db
      $export_history = new EdiExportHistory;
      $export_history->edi_export_id = $edi_export_id;
      $export_history->edi_status = $edi_status;
      if ($error != '') {
        $export_history->remarks = $error;
      }
      $export_history->created_by = $created_by;
      $export_history->save();

      return $export_history;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $edi = EdiExport::with('edi_export_details','edi_export_histories')
                      ->whereDate('created_at', '>=', date('Y-m-d'))
                      ->whereDate('created_at', '<=', date('Y-m-d'))
                      ->get();

      return view('main.rollstock.edi.index')->withDatas($edi)
              ->withDateFrom(date('Y-m-d'))
              ->withDateTo(date('Y-m-d'));
    }

    public function showHistory(Request $request)
    {
      $edi = EdiExport::with('edi_export_details','edi_export_histories')
                      ->whereDate('created_at', '>=', $request->date_from)
                      ->whereDate('created_at', '<=', $request->date_to)
                      ->get();

      return view('main.rollstock.edi.index')->withDatas($edi)
              ->withDateFrom($request->date_from)
              ->withDateTo($request->date_to);

    }

    public function export_process($exec_type){
      $results = VerifyRoll::with([
                            'receive_roll',
                            'receive_roll.supplier',
                            'receive_roll.purchase_order',
                            'receive_roll.site'
                          ])
                          ->where('exported', false)
                          ->take(1000)
                          ->get();
      // dd($results);

      $this->export_edi($exec_type, $results);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $data = EdiExport::findOrFail($id);
      $details = EdiExportDetail::with('verify_roll','verify_roll.receive_roll','verify_roll.receive_roll.supplier')->where('edi_export_id',$id)->get();
      // dd($details);
      return view('main.rollstock.edi.show')->withData($data)->withDetails($details);
    }
}
