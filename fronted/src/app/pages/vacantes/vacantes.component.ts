// src/app/components/vacantes/vacantes.component.ts

import { CommonModule } from '@angular/common';
import { MenuComponent } from '../menu/menu.component';
import { Component, OnInit, ViewChild, ElementRef, AfterViewInit } from '@angular/core';
import { FormsModule } from '@angular/forms';

import { VacantesService, Vacante } from '../../services/vacantes.service';
// <<<< DESCOMENTADO: Importar el servicio de Postulaciones >>>>
import { PostulacionesService } from '../../services/postulaciones.service';
// Importar la interfaz Postulacion si la definiste en el servicio de postulaciones
// import { Postulacion } from '../../services/postulaciones.service'; // Opcional si no usas el tipado aquí

import { catchError } from 'rxjs/operators';
import { of } from 'rxjs';

// Asumiendo que Bootstrap está disponible globalmente para usar su funcionalidad de modal
declare var bootstrap: any;


@Component({
  selector: 'app-vacantes',
  standalone: true,
  imports: [CommonModule, MenuComponent, FormsModule],
  templateUrl: './vacantes.component.html',
  styleUrls: ['./vacantes.component.scss']
})
export class VacantesComponent implements OnInit, AfterViewInit {
  vacantes: Vacante[] = [];
  vacanteSeleccionada: Vacante | any = {};
  errorCargandoVacantes: boolean = false;

  @ViewChild('confirmacionModal') confirmacionModalElement!: ElementRef;
  private confirmacionModal: any;


  // <<<< DESCOMENTADO: Inyectar el servicio de Postulaciones >>>>
  constructor(
    private vacantesService: VacantesService,
    private postulacionesService: PostulacionesService // <<<< Descomentada esta línea >>>>
    ) {
    // La lógica de carga ahora va en ngOnInit
  }

  ngOnInit(): void {
    this.cargarVacantes();
  }

   ngAfterViewInit(): void {
     if (this.confirmacionModalElement) {
       this.confirmacionModal = new bootstrap.Modal(this.confirmacionModalElement.nativeElement);
     } else {
       console.error("Elemento del modal de confirmación no encontrado.");
     }
  }

  cargarVacantes(): void {
    this.vacantesService.getVacantes()
      .pipe(
        catchError(error => {
          console.error('Error al cargar las vacantes:', error);
          this.errorCargandoVacantes = true;
          return of([]);
        })
      )
      .subscribe(
        (data: Vacante[]) => {
          this.vacantes = data;
          if (this.vacantes.length > 0) {
            this.vacanteSeleccionada = this.vacantes[0];
          } else {
             this.vacanteSeleccionada = {};
          }
          this.errorCargandoVacantes = false;
        }
      );
  }

  seleccionarVacante(vacante: Vacante): void {
    this.vacanteSeleccionada = vacante;
  }

  postularme(): void {
    if (this.vacanteSeleccionada && this.vacanteSeleccionada.idVacantes) {
       if (this.confirmacionModal) {
         this.confirmacionModal.show();
       } else {
         console.error("El modal de confirmación no se inicializó correctamente.");
         alert("Por favor, selecciona una vacante antes de postularte."); // Ejemplo simple
       }
    } else {
        console.warn("No hay una vacante seleccionada para postularse.");
        alert("Por favor, selecciona una vacante antes de postularte."); // Ejemplo simple
    }
  }

  confirmarPostulacion(): void {
    console.log('Confirmada la postulación para la vacante:', this.vacanteSeleccionada.nomVacante);

    if (this.confirmacionModal) {
      this.confirmacionModal.hide();
    }

    // Preparar los datos a enviar
    const postulacionData = {
        vacantesId: this.vacanteSeleccionada.idVacantes
        // Añade userId aquí si tu backend lo necesita y lo obtienes de tu lógica de autenticación
        // userId: this.authService.currentUserId, // Ejemplo
    };

    // <<<< DESCOMENTADO: Llamar al servicio de Postulaciones y suscribirse >>>>
    this.postulacionesService.crearPostulacion(postulacionData).subscribe(
        (response) => {
            // Este bloque se ejecuta si la petición POST fue exitosa (código 2xx)
            console.log('Postulación enviada con éxito:', response);
            alert('¡Te has postulado con éxito a la vacante: ' + this.vacanteSeleccionada.nomVacante + '!');
            // Puedes añadir lógica adicional aquí después de una postulación exitosa
        },
        (error) => {
            // Este bloque se ejecuta si la petición POST falló
            console.error('Error al enviar la postulación:', error);
            // Verifica si el error contiene detalles de validación (código 422) si tu backend los envía
            if (error.status === 422 && error.error && error.error.errors) {
                 let validationErrors = 'Errores de validación:\n';
                 for (const key in error.error.errors) {
                     if (error.error.errors.hasOwnProperty(key)) {
                         validationErrors += `- ${error.error.errors[key].join(', ')}\n`;
                     }
                 }
                 alert('Error al enviar la postulación:\n' + validationErrors);
            } else {
                alert('Ocurrió un error al postularte. Por favor, inténtalo de nuevo.');
            }
        }
    );
    // <<<< Fin del bloque descomentado >>>>

    // La lógica temporal de console.log se elimina ahora que usamos el servicio
    // console.log("DEBUG: Los siguientes datos serían enviados al backend para crear una postulación:", postulacionData);
  }

   cancelarPostulacion(): void {
     console.log('Postulación cancelada por el usuario.');
     if (this.confirmacionModal) {
       this.confirmacionModal.hide();
     }
   }


  // Funciones eliminarVacante, abrirModalAgregar y editarVacante (si existían)
  // ...
}