import { Component, OnInit } from '@angular/core';
import { MenuComponent } from "../menu/menu.component";
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { Observable, catchError, throwError } from 'rxjs';

// Esta interfaz no se usa directamente para enviar FormData, pero define la estructura esperada si enviaras JSON
// interface VacationRequestJsonData {
//   descrip: string;
//   archivoNombre: string | null;
//   fechaInicio: string;
//   fechaFinal: string;
//   contratoId: number;
// }

// Esta interfaz es para mostrar los datos en la lista local (puede incluir el objeto File)
interface SolicitudVacacionesDisplay {
    idVacaciones?: number;
    descrip: string;
    archivo: File | null; // Aquí guardamos el objeto File seleccionado localmente
    fechaInicio: string;
    fechaFinal: string;
    estado: 'pendiente' | 'rechazado' | 'aprobado' | 'desconocido';
    // **** LÍNEA CORREGIDA ****
    // Ahora permite 'number', 'null' o 'undefined' (debido al '?')
    contratoId?: number | null;
}


@Component({
  selector: 'app-formvacaciones',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule, HttpClientModule],
  templateUrl: './formvacaciones.component.html',
  styleUrls: ['./formvacaciones.component.scss'] // Asegúrate de que este archivo existe
})
export class FormvacacionesComponent implements OnInit {
  descrip: string = '';
  // La propiedad para mantener el objeto File seleccionado
  archivo: File | null = null;
  fechaInicio: string = '';
  fechaFinal: string = '';

  // Debes obtener este valor antes de enviar el formulario.
  // Puede venir de un servicio de autenticación/usuario o de otra parte de tu app.
  // TODO: Obtener el contratoId real del usuario/contexto
  contratoId: number | null = 1; // Valor de ejemplo, cámbialo por la lógica real

  solicitudesVacaciones: SolicitudVacacionesDisplay[] = [];

  // Inyectamos HttpClient para poder hacer peticiones HTTP
  constructor(private http: HttpClient) { }

  ngOnInit(): void {
    // Lógica de inicialización (cargar contratoId, solicitudes existentes, etc.)
    // TODO: Llama aquí a la lógica para obtener el contratoId real
    // this.cargarContratoIdUsuarioActual();
    // TODO: Opcional: Llama aquí a la lógica para cargar solicitudes existentes
    // this.cargarSolicitudesExistentes();
  }

  // Método para manejar la selección del archivo
  onFileSelected(event: Event): void {
    const element = event.target as HTMLInputElement;
    let file: File | null = null;
    if (element.files && element.files.length > 0) {
      file = element.files[0];
       // TODO: Opcional: Validar tamaño o tipo de archivo aquí antes de asignarlo
       // if (file.size > 5 * 1024 * 1024) { // Ejemplo: 5MB
       //   alert('El archivo es demasiado grande.');
       //   this.archivo = null; // Limpiar el archivo seleccionado
       //   element.value = ''; // Limpiar visualmente el input file
       //   return;
       // }
       // if (!file.type.includes('image/')) { // Ejemplo: solo permitir imágenes
       //    alert('Solo se permiten archivos de imagen.');
       //    this.archivo = null;
       //    element.value = '';
       //    return;
       // }
    }
    this.archivo = file;
    console.log('Archivo seleccionado:', this.archivo);
  }

  enviarSolicitud(): void {
    // 1. Validar que tenemos el contratoId
    if (this.contratoId === null) {
        console.error("El contratoId no está definido. No se puede enviar la solicitud.");
        alert("Error: No se pudo obtener la información del contrato. Por favor, intenta recargar la página o contacta al soporte.");
        return;
    }

    // 2. Crear el objeto FormData
    const formData = new FormData();

    // 3. Añadir los campos del formulario a FormData
    // Las claves ('descrip', 'fechaInicio', etc.) deben coincidir con
    // los nombres que tu backend espera para estos datos.
    formData.append('descrip', this.descrip);
    formData.append('fechaInicio', this.fechaInicio); // Envía la fecha como string 'YYYY-MM-DD'
    formData.append('fechaFinal', this.fechaFinal);   // Envía la fecha como string 'YYYY-MM-DD'
    formData.append('contratoId', this.contratoId.toString()); // Envía el número como string

    // 4. Añadir el archivo si hay uno seleccionado
    if (this.archivo) {
      // El primer argumento 'archivo' es la clave que tu backend usará para acceder al archivo subido
      // El segundo argumento es el objeto File
      // El tercer argumento (opcional pero recomendado) es el nombre del archivo
      formData.append('archivo', this.archivo, this.archivo.name);
    } else {
        // Si el archivo no es obligatorio y no se seleccionó ninguno,
        // puedes enviar un indicador o simplemente no añadir el campo 'archivo'.
        // Si la columna 'archivo' en tu BD es NOT NULL (como vimos), tu backend debe
        // guardar una cadena vacía o un valor por defecto si no se envía archivo.
        console.log("No se seleccionó ningún archivo adjunto.");
         // No añadimos 'archivo' si no hay archivo para evitar enviar 'null' o ''.
         // El backend lo manejará si la validación permite archivo 'nullable'.
    }


    console.log('Enviando FormData al backend...');

    // 5. Definir la URL de tu endpoint de backend
    // **** CAMBIA ESTA URL por la URL base real de tu servidor Laravel ****
    // Por ejemplo, si tu Laravel corre en http://localhost:8000
    const backendUrl = 'http://localhost:8000/api/solicitudes-vacaciones-con-archivo'; // <<-- URL CORREGIDA (AJUSTA HOST/PUERTO SI ES DIFERENTE)


    // 6. Realizar la solicitud HTTP POST usando HttpClient
     this.http.post<any>(backendUrl, formData).pipe( // <<-- Enviamos formData
       // Manejo de errores: si la solicitud falla
       catchError((error: HttpErrorResponse) => { // Tipamos el error como HttpErrorResponse
         console.error('Error al enviar la solicitud de vacaciones:', error);
         let errorMessage = 'Hubo un error al enviar la solicitud.';

         if (error.status === 422 && error.error && error.error.errors) {
             // Error de validación del backend de Laravel (código 422)
             errorMessage = 'Errores de validación:\n';
             for (const field in error.error.errors) {
                 if (error.error.errors.hasOwnProperty(field)) {
                     errorMessage += `${field}: ${error.error.errors[field].join(', ')}\n`;
                 }
             }
         } else if (error.status === 404) {
              // Error 404 - Not Found (podría ser que la URL sigue mal o el backend no corre)
              errorMessage = `Error 404: No se encontró el recurso. Verifica la URL: ${backendUrl}`;
         }
          else if (error.error instanceof ErrorEvent) {
           // Error del lado del cliente (ej. problemas de red)
           errorMessage = `Error de red o cliente: ${error.error.message}`;
         } else {
           // Error del lado del servidor (500, 400, etc. que no son 422/404 específicos)
           errorMessage = `Error del servidor: ${error.status} - ${error.statusText || ''}`;
            if (error.error && error.error.message) {
              errorMessage += `\nMensaje del backend: ${error.error.message}`;
           }
         }

         alert(errorMessage); // Muestra una alerta con el mensaje de error
         // Propagamos el error para que el suscriptor (si lo hubiera) también lo reciba
         return throwError(() => new Error('Error enviando solicitud'));
       })
     )
     // 7. Suscribirse a la respuesta del backend
     .subscribe(response => {
       console.log('Solicitud enviada con éxito. Respuesta del backend:', response);
       // 8. Manejar la respuesta exitosa
       // La 'response' debería contener un mensaje de éxito y, opcionalmente, los datos guardados.
       // TODO: Actualizar la UI (lista de solicitudes, mensajes de éxito, etc.) basándote en la respuesta.

       // Ejemplo: Añadir la solicitud a la lista local para mostrar (usando los datos enviados + posible ID/estado del backend)
       // Idealmente, usarías 'response.data' si el backend devuelve el objeto guardado
       const solicitudGuardadaLocal: SolicitudVacacionesDisplay = {
           idVacaciones: response.data?.idVacaciones, // Usar el ID si el backend lo devuelve en response.data
           descrip: this.descrip, // O response.data?.descrip
           archivo: this.archivo, // Usamos el objeto File local para mostrar su nombre, o response.data?.archivo si backend devuelve la ruta/nombre
           fechaInicio: this.fechaInicio, // O response.data?.fechaInicio
           fechaFinal: this.fechaFinal,   // O response.data?.fechaFinal
           estado: response.data?.estado || 'pendiente', // Usar el estado del backend si lo devuelve, si no, 'pendiente'
           contratoId: this.contratoId // O response.data?.contratoId
       };
        // Asegúrate de que la respuesta del backend indica éxito antes de hacer esto
        // El código de estado 201 (Created) que devuelve el backend es un buen indicador.
       if (response.status === 201 || (response.message && response.message.includes('guardada con éxito'))) { // Verifica la respuesta esperada
          this.solicitudesVacaciones.push(solicitudGuardadaLocal);
          alert('Solicitud de vacaciones enviada con éxito.');
          this.limpiarFormulario(); // Limpiar el formulario
       } else {
           // Si el backend devuelve algo que no es un error 4xx/5xx pero tampoco el éxito esperado
           console.warn('Respuesta del backend inesperada:', response);
           alert('Solicitud enviada, pero la respuesta del servidor no fue la esperada.');
           // Podrías decidir no limpiar el formulario o no añadir a la lista local en este caso
       }


     });
  }

  // Método para limpiar el formulario
  limpiarFormulario(): void {
    this.descrip = '';
    this.archivo = null; // Resetea la propiedad del archivo
    this.fechaInicio = '';
    this.fechaFinal = '';
    // contratoId NO se limpia ya que viene del contexto del usuario

    // Lógica para limpiar visualmente el input type="file"
     const fileInput = document.getElementById('archivo') as HTMLInputElement;
     if (fileInput) {
       fileInput.value = ''; // Esto es la forma estándar de resetear un input file
     }

     // Si usas NgForm en tu HTML (#vacacionesForm="ngForm")
     // y pasas el formulario a esta función, podrías hacer:
     // limpiarFormulario(form?: NgForm): void { ... if (form) form.resetForm(); }
     // form.resetForm() reseteará todos los campos controlados por ngModel.
  }

  // TODO: Método para obtener contratoId del usuario actual (ejecutar en ngOnInit)
  cargarContratoIdUsuarioActual(): void {
      // Aquí iría la lógica para obtener el contratoId (ej. llamando a un servicio de usuario)
      console.log("TODO: Implementar lógica para cargar contratoId del usuario actual");
      // Ejemplo: this.userService.getUsuarioActual().subscribe(usuario => {
      //    this.contratoId = usuario.contratoId;
      // }, error => {
      //    console.error("No se pudo cargar el contratoId del usuario", error);
      //    // Manejar el error, quizás deshabilitar el formulario o mostrar un mensaje
      // });
  }

  // TODO: Método para cargar solicitudes de vacaciones existentes para el usuario (opcional, en ngOnInit)
   cargarSolicitudesExistentes(): void {
       // Aquí iría la lógica para obtener las solicitudes ya guardadas del backend
       // para mostrarlas en la lista 'solicitudesVacaciones'.
       // console.log("TODO: Implementar lógica para cargar solicitudes existentes");
       // Ejemplo: if (this.contratoId !== null) {
       //   this.vacacionesService.getSolicitudesPorContrato(this.contratoId).subscribe(solicitudes => {
       //       this.solicitudesVacaciones = solicitudes; // Asumiendo que el backend devuelve el formato SolicitudVacacionesDisplay (quizás con el nombre del archivo en lugar del objeto File)
       //   }, error => {
       //       console.error("No se pudieron cargar las solicitudes existentes", error);
       //   });
       // }
   }

}