<?php

namespace App\Http\Controllers;

use App\Patient;
use App\PatientImage;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientsController extends Controller
{
    public function index(){

        return view('patients.index');
        
    }
    public function get_patients(){
        $patients = Patient::with('images')
        ->with('creator')
        ->with('updated_by')
        ->orderBy('priority','asc')
        ->get();
        return response()->json(compact('patients'));
    }
    public function store_patient(Request $r){
        // dd(auth()->user());
        // dd($r->all());
        Log::info('Paciente ingresado: '.$r->all());
        $last_patient = Patient::orderBy('priority','desc')->first();

        $p               = new Patient();
        $p->name         = $r->name;
        $p->diagnostic   = $r->diagnostic;
        $p->phone_number = $r->phone_number;
        $p->created_by   = Auth::user()->id;
        $p->updated_by   = Auth::user()->id;
        $p->priority     = $r->priority ?? $last_patient->priority + 1;
       
        // $pacientes = Patient::where('priority', '<=', $p->priority)->get();
        // foreach($pacientes as $paciente){
        //     $paciente->priority = $paciente->priority - 1;
        //     $paciente->save();
        // }

        $p->save();
        // dd($p->id);
        if($r->image){
            $url                 = Storage::putFile('public', new File($r->image));
            $imagen              = new PatientImage();
            $imagen->url         = $url;
            $imagen->patient_id  = $p->id;
            $imagen->uploaded_by = $p->updated_by;
            $imagen->save();
        }
        // foreach ($r->images as $key => $archivo) {
        //     $url                 = Storage::putFile('public', new File($archivo));
        //     $imagen              = new PatientImage();
        //     $imagen->url         = $url;
        //     $imagen->patient_id  = $p->id;
        //     $imagen->uploaded_by = $p->updated_by;
        //     $imagen->save();
        // }
    

        return response()->json('Paciente creado correctamente');
    }
    public function up_priority(Request $r){
        Log::info($r->all());
        $p = Patient::find($r->id);
        if(!$p){
            return 'error';
        }
        if($p->priority == 1){
            return response()->json('ya esta en prioridad 1');
        }
        $p->priority = $p->priority -1;
        $p->updated_by   = Auth::user()->id;
        // $pacientes = Patient::where('priority', '<=', $p->priority)->get();
        // foreach($pacientes as $paciente){
        //     if($p->priority > 1){
        //         $paciente->priority = $paciente->priority + 1;
        //         $paciente->save();
        //     }
        // }
        $p->save();
        return response()->json('actualizado');
    }
    public function down_priority(Request $r){
        Log::info($r->all());
        $p = Patient::find($r->id);
        if(!$p){
            return 'error';
        }
        $last_patient = Patient::orderBy('priority','desc')->first();
        if($p->priority == $last_patient->priority){
            return response()->json('ya esta al final de la prioridad');
        }
        $p->updated_by   = Auth::user()->id;
        $p->priority = $p->priority + 1;
        $pacientes = Patient::where('priority', '<=', $p->priority)->get();
        // foreach($pacientes as $paciente){
        //     if($p->priority < $last_patient->priority){
        //         $paciente->priority = $paciente->priority - 1;
        //         $paciente->save();
        //     }
        // }
        $p->save();
        return response()->json('actualizado');
    }
    public function dar_de_alta(Request $r){

        $p = Patient::find($r->id);
        $p->updated_by   = Auth::user()->id;
        $p->save();
        if(!$p){
            return 'error';
        }

        $p->delete();
        return response()->json('paciente dado de alta');
    }
}
