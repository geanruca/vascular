<template>

  <div class="container">

      <h4>Ingrese datos del paciente:</h4>  
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"  id="basic-addon1">Nombre</span>
        </div>
        <input v-model='name' type="text" class="form-control" placeholder="Nombre" aria-label="Nombre" aria-describedby="basic-addon1">
      </div>

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"  id="basic-addon1">Telefono</span>
        </div>
        <input v-model='phone_number' type="text" class="form-control" placeholder="Telefono" aria-label="Telefono" aria-describedby="basic-addon1">
      </div>

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text"  id="basic-addon1">Diagnóstico</span>
        </div>
        <input v-model='diagnostic' type="text" class="form-control" placeholder="Diagnóstico" aria-label="Diagnóstico" aria-describedby="basic-addon1">
      </div>

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <label class="input-group-text" for="inputGroupSelect01">Prioridad del paciente</label>
        </div>
        <select v-model="priority" class="custom-select" id="inputGroupSelect01">
          <option selected value="1">Urgencia</option>
          <option value="3">Puede esperar</option>
          <option value="5">Puede esperar mas</option>
        </select>
      </div>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text">Imagen</span>
        </div>
        <div class="custom-file">
          <input type="file" @change='onFileChange' name='images[]' class="custom-file-input" id="inputGroupFile01">
          <label class="custom-file-label" for="inputGroupFile01">Buscar imagen</label>
        </div>
      </div>
      <div class="input-group mb-3">
        <button type="submit" class="btn btn-primary" @submit='create_patient()' @click='create_patient()'>Ingresar paciente</button>
      </div>
      <hr>

      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped table-hover">

          
          <thead class="thead-dark ">

            <tr>
              <th>Nombre</th>
              <th>Diagnostico</th>
              <th>Telefono</th>
              <th>Ingresado por</th>
              <th>Fecha ingreso</th>
              <th>Actualizado por</th>
              <th>Fecha actualización</th>
              <th>Imagen</th>
              <th>Prioridad</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody class="table-striped">

            <tr v-for="(p, key) in patients">
              <td>{{p.name}}</td>
              <td>{{p.diagnostic}}</td>
              <td>{{p.phone_number}}</td>
              <td>{{p.creator.name}}</td>
              <td>{{p.created_at | arreglarFecha}}</td>
              <td>{{p.updated_by.name}}</td>
              <td>{{p.updated_at | arreglarFecha}}</td>
              <td>
                <span v-if="p.images[0]">
                  <a target="_blank" :href="'/storage/'+p.images[0].url.split('/')[1]"> Link! </a> 
                </span>
                
                </td>
              <td>{{p.priority}}</td>
              <td>
                <button @click='up_priority(p.id)' class="btn btn-sm btn-primary"><i class="fa fa-chevron-up" aria-hidden="true"></i></button>
                <button @click='down_priority(p.id)' class="btn btn-sm btn-primary"><i class="fa fa-chevron-down" aria-hidden="true"></i></button>
              </td>
              <td>
                <button class="btn btn-sm btn-danger" @click='dar_de_alta(p.id)'>Dar de alta</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

  </div>
</template>


<script>
import miniToastr from 'mini-toastr';
miniToastr.init();
  export default {
    data() {
      return {
        filters: [
          { key: 'current_status', input: null, name: 'current status'},
          { key: 'deliveryzone',   input: null, name: 'delivery zone'},
          { key: 'pickupzone',     input: null, name: 'pickup zone'},
          { key: 'consignee_code', input: null, name: 'consignee code'},
        ],
        search             : '',
        data               : null,
        traces             : null,
        msg                : null,
        modalURL           : null,
        selected_filter    : null,
        filter_search_by   : null,
        filter_style_search: null,
        filter_search_value: null,
        filter_pickupfrom  : null,
        filter_pickupto    : null,
        filter_deliverfrom : null,
        filter_deliverto   : null,
        errors             : null,
        form_pickupfrom    : '',
        form_pickupto      : '',
        form_deliverfrom   : '',
        form_deliverto     : '',
        fecha_actual       : null,
        image1             : null,
        image2             : null,
        image3             : null,
        name               : null,
        phone_number       : null,
        diagnostic         : null,
        image3             : null,
        patients           : null,
        priority           : null,
        

        //buscar_nuevo: 0,
        
      }
    },
    mounted() {
      console.log("montado")
      this.get_patients();

      let hoy_dia              = new Date()
      let hoy_dia_milisegundos = Date.parse(hoy_dia)
      let fecha_final          = new Date(hoy_dia_milisegundos)
      let day                  = fecha_final.getDate() 
      let month                = fecha_final.getMonth()

      if(day < 10){
          day = '0'+day.toString();
      }
      if(month < 10){
          month = '0'+month.toString();
      }
      this.fecha_actual = fecha_final.getFullYear().toString() + month + day
      
    },
    filters: {
      filter_capitalize:    function(value) {
        
        return value.toLowerCase().split(' ').map( (i, j) => i.charAt(0).toUpperCase()+i.slice(1)).join(' ');
      },
      filtroDocumento:      function (value) {
          value = value.split('/')
          //console.log(value)

          value = value[value.length-1]
          //console.log(value)
          //value = value.toString()
          return value;
      },
      filtroSacarComillas:  function(value) {
          value = value.split('"');
          value = value[1];
          return value;
        
      },
      minusculas:           function(palabra){
        if(palabra != null){

          return palabra.toLowerCase()
        }
      },
      arreglarFecha:        function(fecha){
        let fecha_final =  Date.parse(fecha)
        fecha_final = new Date(fecha_final)
        if(fecha_final == null){
          return null;
        }
        let seg = fecha_final.getSeconds()
        let min = fecha_final.getMinutes()
        let hour = fecha_final.getHours()
        if(seg < 10){
            seg = '0'+seg;
        }
        if(min < 10){
            min = '0'+min;
        }
        if(hour < 10){
            hour = '0'+hour;
        }
        return fecha_final.getMonth() + '/' +fecha_final.getDate() + '/'+fecha_final.getFullYear() + ' ' + hour  + ':' + min  + ':' + seg;
      },
    },
    methods: {
      create_patient(){
        var formData2 = new FormData();
            formData2.append('image',this.image1);
            formData2.append('name',this.name);
            formData2.append('priority',this.priority);
            formData2.append('diagnostic',this.diagnostic);
            formData2.append('phone_number',this.phone_number);
            // formData2.append('_method', 'PATCH');
            axios.post('api/patients/store_patient', formData2)
            .then(response=>{
                
                miniToastr.success(response.msg, 'Actualizado')
                this.get_patients()
            })
        .catch(error => {
          //console.log(error.response.data.message)
          //var self = this
          this.msg = 'aca va el mensaje de error de server';
          //this.buscar_nuevo = 0;
          //this.search = null;
          console.log(this.msg)
          //return Promise.reject(error)
        })

      },
      up_priority(id){
        var formData2 = new FormData();
            formData2.append('id',id);
            axios.post('api/patients/up_priority', formData2)
            .then(response=>{
                
              this.get_patients()
              miniToastr.success(response.msg, 'Actualizado')
            })
        .catch(error => {
          //console.log(error.response.data.message)
          //var self = this
          this.msg = 'aca va el mensaje de error de server';
          //this.buscar_nuevo = 0;
          //this.search = null;
          console.log(this.msg)
          //return Promise.reject(error)
        })

      },
      down_priority(id){
        var formData2 = new FormData();
            formData2.append('id',id);
            axios.post('api/patients/down_priority', formData2)
            .then(response=>{
                
              this.get_patients()
              miniToastr.success(response.msg, 'Actualizado')
            })
        .catch(error => {
          this.msg = 'aca va el mensaje de error de server';
          console.log(this.msg)
        })

      },
      dar_de_alta(id){
        var formData2 = new FormData();
            formData2.append('id',id);
            axios.post('api/patients/dar_de_alta', formData2)
            .then(response=>{
                
              this.get_patients()
              miniToastr.success(response.msg, 'Pacinte dado de alta')
            })
        .catch(error => {
          this.msg = 'aca va el mensaje de error de server';
          console.log(this.msg)
        })

      },
     get_patients(){
        axios.get('/api/patients/get_patients')
        .then(response => {
            this.patients = response.data.patients
            //this.data = null
            console.log(response.data.patients)
        })
        .catch(error => {
          this.msg = 'aca va el mensaje de error de server';
          //this.buscar_nuevo = 0;
          this.search = null;
          console.log(this.msg)
          //return Promise.reject(error)
        })
      },
      open_modal(llave_actual){
          // me va a llegar la llave del input que estoy modificando => deliveryzone
          for (let i = 0; i < this.filters.length; i++) {
            if(this.filters[i].key == llave_actual){
              this.selected_filter = this.filters[i]
            }
          }
          //this.filters 
      },
      save_selected(dato){
        this.selected_filter.input = dato;
          for (let i = 0; i < this.filters.length; i++) {
            if(this.filters[i].key == this.selected_filter.key){
              this.filters[i] = this.selected_filter
            }
          }
      },
      onFileChange(e){
        // console.log(e)
          var files = e.target.files || e.dataTransfer.files;
          if (!files.length)
            return;
          this.image1 = files[0];
      },
      documentoModal(item){
        this.modalURL = item;
      },
      printinfo(){
          var mywindow = window.open('', 'PRINT', 'height=800,width=900');
          mywindow.document.write('<html><head><title>' + document.title  + '</title>');
          mywindow.document.write('<link rel="stylesheet" href="./css/app.css" type="text/css" />')
          mywindow.document.write('</head><body >');
          mywindow.document.write('<h1>' + document.title  + '</h1>');
          mywindow.document.write(document.getElementById('mi_impresion').innerHTML);
          mywindow.document.write('</body></html>');

          mywindow.document.close(); // necessary for IE >= 10
          mywindow.focus(); // necessary for IE >= 10*/

          mywindow.print();
          //mywindow.close();

          return true;
      },
      open_files(url){
        console.log(url)
          var mywindow = window.open(url, 'PRINT', 'height=600,width=700');
          //mywindow.document.write("<a href="+url+">Link</a>");
          //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
          //mywindow.document.write('<link rel="stylesheet" href="./css/app.css" type="text/css" />')
          //mywindow.document.write('</head><body >');
          //mywindow.document.write('<h1>' + document.title  + '</h1>');
          //mywindow.document.write("<embed src="+url+"type='application/pdf' width='100%' height='100%'>");
          //mywindow.document.write('</body></html>');

          mywindow.document.close(); // necessary for IE >= 10
          mywindow.focus(); // necessary for IE >= 10*/

          //mywindow.close();

          return true;
      },
    },
  }
  
</script>
<style>

</style>
