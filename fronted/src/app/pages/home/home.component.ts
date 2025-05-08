import { Component, OnInit } from '@angular/core';
import { MenuComponent } from "../menu/menu.component";
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { UserService } from '../../services/user.service';
import { User } from '../../models/user';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [MenuComponent, FormsModule, CommonModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
  isModalVisible: boolean = false;
  user: User = {
    primerNombre: '',
    segundoNombre: '',
    primerApellido: '',
    segundoApellido: '',
    usuario: '',
    tipoDocumento: '',
    numeroDocumento: '',
    fechaNacimiento: '',
    numeroHijos: null,
    contactoEmergencia: '',
    numeroContactoEmergencia: '',
    email: '',
    direccion: '',
    telefono: '',
    nacionalidad: '',
    eps: '',
    genero: '',
    estadoCivil: '',
    pensiones: ''
  };

  constructor(private userService: UserService) {}

  ngOnInit(): void {
    this.loadUserData();
  }

  loadUserData(): void {
    this.userService.getUserProfile().subscribe((userData: any) => {
      console.log('Datos recibidos:', userData);
      this.user = {
        primerNombre: userData.primerNombre,
        segundoNombre: userData.segundoNombre,
        primerApellido: userData.primerApellido,
        segundoApellido: userData.segundoApellido,
        usuario: '', // si no lo manejas, déjalo vacío o elimina del modelo
        tipoDocumento: userData.tipo_documento?.nombreTipoDocumento || '',
        numeroDocumento: userData.numDocumento,
        fechaNacimiento: userData.fechaNac,
        numeroHijos: userData.numHijos,
        contactoEmergencia: userData.contactoEmergencia,
        numeroContactoEmergencia: userData.numContactoEmergencia,
        email: userData.email,
        direccion: userData.direccion,
        telefono: userData.telefono,
        nacionalidad: userData.nacionalidadId,
        eps: userData.epsCodigo,
        genero: userData.genero?.nombreGenero || '',
        estadoCivil: userData.estadoCivilId,
        pensiones: userData.pensionesCodigo
      };
    }, error => {
      console.error('Error al obtener el perfil del usuario', error);
    });
  }

  openModal(): void {
    this.isModalVisible = true;
  }

  closeModal(): void {
    this.isModalVisible = false;
  }

  updateInfo(): void {
    this.userService.updateUserProfile(this.user).subscribe(response => {
      console.log('Perfil actualizado:', response);
      this.closeModal();
    }, error => {
      console.error('Error al actualizar el perfil', error);
    });
  }
}
