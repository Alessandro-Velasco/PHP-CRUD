<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $datos['empleados']=Empleado::paginate(1);
        return view('empleado.index', $datos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        //

        $campos=[
            'Nombre'=>'required|string|max:100',
            'ApellidoPaterno'=>'required|string|max:100',
            'ApellidoMaterno'=>'required|string|max:100',
            'Correo'=>'required|email',
            'Foto'=>'required|max:4000|mimes:jpeg,png,jpg',

        ];

        $mensaje=[
            'required'=>'El :attribute es requerido',
            'Foto.required'=>'La foto es requerida',
        ];

        $this->validate($request, $campos, $mensaje);
        //$datosEmpleado = request()->all();
        $datosEmpleado = request()->except('_token');

        if($request->hasFile('Foto')){
            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads','public');
        }


        Empleado::insert($datosEmpleado);

        //return response()->json($datosEmpleado);
        return redirect('empleado')->with('mensaje','Empleado agregado con exito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $empleado=Empleado::findOrFail($id);

        return view('empleado.edit', compact('empleado') );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $campos=[
            'Nombre'=>'required|string|max:100',
            'ApellidoPaterno'=>'required|string|max:100',
            'ApellidoMaterno'=>'required|string|max:100',
            'Correo'=>'required|email',
            'Foto'=>'required|max:4000|mimes:jpeg,png,jpg',

        ];

        $mensaje=[
            'required'=>'La :attribute es requerida',

        ];

        if($request->hasFile('Foto')){

            $campos=['Foto'=>'required|max:4000|mimes:jpeg,png,jpg'];
            $mensaje=['Foto.required'=>'La foto es requerida'];
        }

        $this->validate($request, $campos, $mensaje);
        //
        $datosEmpleado = request()->except(['_token', '_method']);
        
        if($request->hasFile('Foto')){
            $empleado=Empleado::findOrFail($id);

            Storage::delete('public/'.$empleado->Foto);

            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads','public');
        }



        Empleado::where('id','=',$id)->update($datosEmpleado);
        $empleado=Empleado::findOrFail($id);
        //return view('empleado.edit', compact('empleado') );
        return redirect('empleado')->with('mensaje','Empleado Modificado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $empleado = Empleado::findOrFail($id);

    // Attempt to delete the file associated with the empleado
    if (Storage::delete('public/' . $empleado->Foto)) {
        
        Empleado::destroy($id);
    }

    return redirect('empleado')->with('mensaje','Empleado borrado');
}
}