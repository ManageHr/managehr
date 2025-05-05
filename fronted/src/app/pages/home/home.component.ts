import { Component, OnInit } from '@angular/core';
import { MenuComponent } from "../menu/menu.component";
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
  isModalVisible: boolean = false;

  // Propiedades para los campos del formulario
  tipoDocumento: string = '';
  numeroDocumento: string = '';
  primerNombre: string = '';
  segundoNombre: string = '';
  primerApellido: string = '';
  segundoApellido: string = '';
  fechaNacimiento: string = '';
  numeroHijos: number | null = null;
  contactoEmergencia: string = '';
  numeroContactoEmergencia: string = '';
  email: string = '';
  direccion: string = '';
  telefono: string = '';
  nacionalidad: string = '';
  eps: string = '';
  genero: string = '';
  estadoCivil: string = '';
  pensiones: string = '';

  constructor() { }

  ngOnInit(): void {
    // Aquí podrías cargar los datos del usuario para prellenar las propiedades
    // Ejemplo:
    this.tipoDocumento = 'Cédula de Ciudadanía';
    this.numeroDocumento = '987654321';
    this.primerNombre = 'Ana';
    this.segundoNombre = 'Sofía';
    this.primerApellido = 'Pérez';
    this.segundoApellido = 'Gómez';
    this.fechaNacimiento = '1988-07-20';
    this.numeroHijos = 1;
    this.contactoEmergencia = 'Juan Rodríguez';
    this.numeroContactoEmergencia = '+57 310 987 6543';
    this.email = 'ana.perez@correo.com';
    this.direccion = 'Avenida Siempreviva 742';
    this.telefono = '+57 315 112 2334';
    this.nacionalidad = 'Colombiana';
    this.eps = 'Salud Total';
    this.genero = 'Femenino';
    this.estadoCivil = 'Casada';
    this.pensiones = 'Colpensiones';
  }

  openModal(): void {
    this.isModalVisible = true;
    // Aquí podrías cargar los datos actuales en las propiedades del formulario si es necesario
    // Por ejemplo, si quieres que al abrir el modal los campos ya tengan la información actual
  }

  closeModal(): void {
    this.isModalVisible = false;
  }

  updateInfo(): void {
    // Aquí puedes acceder a los valores de las propiedades (this.tipoDocumento, this.numeroDocumento, etc.)
    // y realizar la lógica para actualizar la información (enviar al backend, etc.)
    console.log('Datos a actualizar:', {
      tipoDocumento: this.tipoDocumento,
      numeroDocumento: this.numeroDocumento,
      primerNombre: this.primerNombre,
      segundoNombre: this.segundoNombre,
      primerApellido: this.primerApellido,
      segundoApellido: this.segundoApellido,
      fechaNacimiento: this.fechaNacimiento,
      numeroHijos: this.numeroHijos,
      contactoEmergencia: this.contactoEmergencia,
      numeroContactoEmergencia: this.numeroContactoEmergencia,
      email: this.email,
      direccion: this.direccion,
      telefono: this.telefono,
      nacionalidad: this.nacionalidad,
      eps: this.eps,
      genero: this.genero,
      estadoCivil: this.estadoCivil,
      pensiones: this.pensiones
    });

    this.closeModal(); // Cierra el modal después de intentar actualizar
  }
}

