import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from 'src/app/services/auth.service';
import { Router } from '@angular/router';

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
    private router: Router
  ) {}

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
      this.mensaje = '❗ Los correos electrónicos no coinciden.';
      return;
    }

    if (this.password !== this.confirmarPassword) {
      this.mensaje = '❗ Las contraseñas no coinciden.';
      return;
    }

    const data = {
      name: this.nombre,
      email: this.email,
      email_confirmation: this.confirmarEmail,
      password: this.password,
      rol: 5, // Asignar rol automáticamente como Proveedor
      password_confirmation: this.confirmarPassword
    };

    this.authService.register(data).subscribe({
      next: (res) => {
        this.mensaje = '✅ Registro exitoso 🎉';
        this.registroExitoso = true;

        // Guardar datos en localStorage
        localStorage.setItem('token', res.token);
        if (res.user) {
          localStorage.setItem('usuario', JSON.stringify(res.user));
        }

        // Redirigir de manera limpia
        this.router.navigate(['/vacantes']).then(() => {
          this.eliminarModalBackdrop(); // Asegúrate de eliminar cualquier overlay residual
        });

        this.resetForm();
      },
      error: (err) => {
        console.error(err);
        if (err.status === 422 && err.error?.errors) {
          const errores = Object.values(err.error.errors).flat().join(' ');
          this.mensaje = `❌ ${errores}`;
        } else {
          this.mensaje = err.error?.message || '⚠️ Error en el registro.';
        }
        this.registroExitoso = false;
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
}
