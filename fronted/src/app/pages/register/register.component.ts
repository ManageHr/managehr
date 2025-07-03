import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from 'src/app/services/auth.service';
import { UsuariosService } from 'src/app/services/usuarios.service';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit, OnDestroy {
  nombre: string = '';
  email: string = '';
  confirmarEmail: string = '';
  password: string = '';
  confirmarPassword: string = '';
  mensaje: string = '';
  registroExitoso: boolean = false;
  numDocumento: string = '';

  primerNombre = '';
  segundoNombre = '';
  primerApellido = '';
  segundoApellido = '';
  vacantes = [
    {
      titulo: 'Desarrollador Frontend',
      salario: '$1,200 USD',
      descripcion: 'Responsable del desarrollo de interfaces modernas con Angular.',
      requisitos: ['HTML', 'CSS', 'JavaScript', 'Angular'],
      imagen: 'https://cdn-icons-png.flaticon.com/512/2721/2721297.png'
    },
    {
      titulo: 'Diseñador UI/UX',
      salario: '$1,000 USD',
      descripcion: 'Diseño de experiencias e interfaces intuitivas.',
      requisitos: ['Figma', 'Sketch', 'Prototipado'],
      imagen: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'
    },
    {
      titulo: 'Backend Node.js',
      salario: '$1,500 USD',
      descripcion: 'Desarrollo y mantenimiento de APIs RESTful.',
      requisitos: ['Node.js', 'Express', 'MongoDB'],
      imagen: 'https://cdn-icons-png.flaticon.com/512/2721/2721298.png'
    },
    {
      titulo: 'QA Tester',
      salario: '$900 USD',
      descripcion: 'Ejecución de pruebas manuales y automatizadas.',
      requisitos: ['Postman', 'Selenium', 'Cypress'],
      imagen: 'https://cdn-icons-png.flaticon.com/512/921/921347.png'
    },
    {
      titulo: 'Scrum Master',
      salario: '$1,800 USD',
      descripcion: 'Facilitador de equipos en entornos ágiles.',
      requisitos: ['Scrum', 'Kanban', 'Gestión de equipos'],
      imagen: 'https://cdn-icons-png.flaticon.com/512/190/190411.png'
    },
    {
      titulo: 'Administrador de Base de Datos',
      salario: '$1,400 USD',
      descripcion: 'Mantenimiento y optimización de bases de datos SQL y NoSQL.',
      requisitos: ['SQL', 'MySQL', 'MongoDB'],
      imagen: 'https://cdn-icons-png.flaticon.com/512/4299/4299956.png'
    },
    {
      titulo: 'DevOps Engineer',
      salario: '$2,000 USD',
      descripcion: 'Implementación de CI/CD y administración en la nube.',
      requisitos: ['Docker', 'Kubernetes', 'AWS', 'CI/CD'],
      imagen: 'https://cdn-icons-png.flaticon.com/512/2721/2721299.png'
    }
  ];

  vacanteSeleccionada: any = null;

  constructor(
    private authService: AuthService,
    private usuariosService: UsuariosService,
    private router: Router
  ) { }

  ngOnInit(): void {
    // Asegúrate de eliminar cualquier clase innecesaria del body al cargar el componente
    document.body.classList.remove('login-background');
    this.eliminarModalBackdrop();
  }

  ngOnDestroy(): void {
    // Limpia cualquier clase aplicada al body cuando el componente sea destruido
    document.body.classList.remove('login-background');
    this.eliminarModalBackdrop();
  }

  seleccionarVacante(vacante: any): void {
    this.vacanteSeleccionada = vacante;
    this.mensaje = '';
    this.registroExitoso = false;
  }

  enviarFormulario(): void {
    
    if (this.email !== this.confirmarEmail) {
      this.mensaje = 'Los correos electrónicos no coinciden.';
      return;
    }

    if (this.password !== this.confirmarPassword) {
      this.mensaje = 'Las contraseñas no coinciden.';
      return;
    }

    if (!this.numDocumento) {
      this.mensaje = 'Debe ingresar su número de documento.';
      return;
    }
    const partes = this.nombre.trim().split(/\s+/); // Elimina espacios extra

    // Limpiar antes de asignar
    this.primerNombre = '';
    this.segundoNombre = '';
    this.primerApellido = '';
    this.segundoApellido = '';

    if (partes.length < 2) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debe ingresar al menos un nombre y un apellido.',
        confirmButtonText: 'Aceptar'
      }).then(() => {
        this.resetForm();
        this.eliminarModalBackdrop();
      });
      return;
    } else if (partes.length === 2) {
      [this.primerNombre, this.primerApellido] = partes;
    } else if (partes.length === 3) {
      [this.primerNombre, this.primerApellido, this.segundoApellido] = partes;
    } else if (partes.length === 4) {
      [this.primerNombre, this.segundoNombre, this.primerApellido, this.segundoApellido] = partes;
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Solo se admiten máximo 2 nombres y 2 apellidos.',
        confirmButtonText: 'Aceptar'
      }).then(() => {
        this.resetForm();
        this.eliminarModalBackdrop();
      });
      return;
    }
    const data = {
      name: this.nombre,
      email: this.email,
      email_confirmation: this.confirmarEmail,
      password: this.password,
      password_confirmation: this.confirmarPassword,
      rol: 5
    };


    this.authService.register(data).subscribe({
      next: (res) => {
        this.mensaje = 'Registro exitoso';
        this.registroExitoso = true;

        
        if (res.user) {
          localStorage.setItem('token', res.token);
          localStorage.setItem('usuario', JSON.stringify(res.user));
          this.crearUsuario(res.user.id); // 👈 crear Usuario con ID del User
        }

        this.router.navigate(['/vacantes']).then(() => {
          this.eliminarModalBackdrop();
        });

        this.resetForm();
      },
      error: (err) => {
        console.error(err);
        if (err.status === 422 && err.error?.errors) {
          const errores = Object.values(err.error.errors).flat().join(' ');
          this.mensaje = errores;
        } else {
          this.mensaje = err.error?.message || 'Error en el registro.';
        }
        this.registroExitoso = false;
      }
    });
  }
  crearUsuario(userId: number): void {
    const partes = this.nombre.trim().split(/\s+/); // Elimina espacios extra

    // Limpiar antes de asignar
    this.primerNombre = '';
    this.segundoNombre = '';
    this.primerApellido = '';
    this.segundoApellido = '';

    if (partes.length < 2) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Debe ingresar al menos un nombre y un apellido.',
        confirmButtonText: 'Aceptar'
      }).then(() => {
        this.resetForm();
        this.eliminarModalBackdrop();
      });
      return;
    } else if (partes.length === 2) {
      [this.primerNombre, this.primerApellido] = partes;
    } else if (partes.length === 3) {
      [this.primerNombre, this.primerApellido, this.segundoApellido] = partes;
    } else if (partes.length === 4) {
      [this.primerNombre, this.segundoNombre, this.primerApellido, this.segundoApellido] = partes;
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Solo se admiten máximo 2 nombres y 2 apellidos.',
        confirmButtonText: 'Aceptar'
      }).then(() => {
        this.resetForm();
        this.eliminarModalBackdrop();
      });
      return;
    }


    const usuarioData = {
      numDocumento: this.numDocumento,
      primerNombre: this.primerNombre,
      segundoNombre: this.segundoNombre,
      primerApellido: this.primerApellido,
      segundoApellido: this.segundoApellido,
      password: this.password,
      fechaNac: '1990-01-01',
      numHijos: 0,
      contactoEmergencia: 'No aplica',
      numContactoEmergencia: '0000000000',
      email: this.email,
      direccion: 'No especificada',
      telefono: '0000000000',
      nacionalidadId: 160,       // COLOMBIA
      epsCodigo: 'EPS001',       // ALIANSALUD (por defecto)
      generoId: 1,               // Masculino
      tipoDocumentoId: 1,        // Cédula
      estadoCivilId: 1,          // Soltero
      pensionesCodigo: '230201', // PROTECCION
      usersId: userId
    };
    
        this.authService.crearUsuario(usuarioData).subscribe({
          next: (res) => {
            console.log('Usuario creado correctamente:', res);
          },
          error: (err) => {
            console.error('Error al crear usuario:', err);
          }
        });
  }



  private eliminarModalBackdrop(): void {
    // Elimina cualquier overlay del modal que pueda permanecer en el DOM
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach((backdrop) => backdrop.remove());
    const openModals = document.querySelectorAll('.modal.show');
    openModals.forEach((modal) => modal.classList.remove('show'));
    document.body.classList.remove('modal-open'); // Elimina la clase que bloquea el scroll
    document.body.style.overflow = ''; // Restaura el scroll
    document.body.style.paddingRight = ''; // Restaura el padding
  }

  private resetForm(): void {
    this.nombre = '';
    this.email = '';
    this.confirmarEmail = '';
    this.password = '';
    this.confirmarPassword = '';
  }

  volver() {
    window.history.back();
  }
  soloLetras(event: KeyboardEvent): boolean {
    const input = event.key;
    const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;

    if (!regex.test(input)) {
      event.preventDefault();
      return false;
    }
    return true;
  }
  soloNumeros(event: KeyboardEvent): boolean {
    const charCode = event.key;
    const regex = /^[0-9]$/;

    if (!regex.test(charCode)) {
      event.preventDefault();
      return false;
    }
    return true;
  }
}
