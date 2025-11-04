import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { UsuariosService, Usuarios } from '../../../services/usuarios.service';
import { AuthService } from '../../../services/auth.service';
import { MenuComponent } from '../../menu/menu.component';
import Swal from 'sweetalert2';

import { NgxPaginationModule } from 'ngx-pagination';
import { FilterNombre } from './filter-nombre';
import { forkJoin } from 'rxjs';
import * as ExcelJS from 'exceljs';
import { Modal } from 'bootstrap';
import { Router } from '@angular/router';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

import { saveAs } from 'file-saver';
import { catchError } from 'rxjs/operators';
import { of } from 'rxjs';
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';

Chart.register(
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
);

declare var bootstrap: any;

@Component({
  selector: 'app-usuarios',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MenuComponent,
    NgxPaginationModule,
    FilterNombre,
  ],
  templateUrl: './usuarios.component.html',
  styleUrls: ['./usuarios.component.scss'],
})
export class UsuariosComponent implements OnInit {
  agregarusuariosModal = false;
  usuarios: Usuarios[] = [];
  userBase: any = null;

  usuariosRolCinco: Usuarios[] = [];
  hojaDeVidaSeleccionada: any = null;

  filtroNombre: string = '';
  filtroNombreExternos: string = '';
  currentPage = 1;
  itemsPerPage = 5;
  nacionalidades: any[] = [];
  eps: any[] = [];
  generos: any[] = [];
  tiposDocumento: any[] = [];
  estadosCiviles: any[] = [];
  pensiones: any[] = [];
  totalPages = 5;
  roles: any[] = [
    { idRol: 1, nombreRol: 'Administrador' },
    { idRol: 2, nombreRol: 'Jefe de personal' },
    { idRol: 3, nombreRol: 'Empleado' },
    { idRol: 4, nombreRol: 'Recursos Humanos' },
    { idRol: 5, nombreRol: 'Externo' },
    { idRol: 7, nombreRol: 'Para borrar nuevo MODEL' },
  ];

  mostrarModal: boolean = false;
  rolNombreSeleccionado: string = '';
  experienciasLaborales: any[] = [];
  usuarioSeleccionado: Usuarios = {
    id: 0,
    primerNombre: '',
    segundoNombre: '',
    primerApellido: '',
    segundoApellido: '',
    telefono: '',
    email: '',
    email_confirmation: '',
    password: '',
    password_confirmation: '',
    direccion: '',
    numDocumento: 0,
    nacionalidadId: 0,
    epsCodigo: '',
    generoId: 0,
    tipoDocumentoId: 0,
    estadoCivilId: 0,
    pensionesCodigo: '',
    rol: 0,
    usersId: 0,
    fechaNacimiento: '',
  };

  usuario: any = {};
  nuevoUsuario: any = {};

  // Agregar estas propiedades a la clase
  archivoActual: string = '';
  cargandoArchivo: boolean = false;
  errorArchivo: string = '';

  constructor(
    private router: Router,
    private usuariosService: UsuariosService,
    private authService: AuthService
  ) {}

  totalPagesExternos: number = 1;

  ngOnInit(): void {
    const token = localStorage.getItem('token');
    const userFromLocal = localStorage.getItem('usuario');

    if (!token || !userFromLocal) {
      this.router.navigate(['/login']);
      return;
    }
    try {
      const tokenPayload = JSON.parse(atob(token.split('.')[1]));
      const expiracion = tokenPayload.exp * 1000; // Convertir a milisegundos
      if (Date.now() >= expiracion) {
        this.mostrarSesionExpirada();
        return;
      }
    } catch (error) {
      console.error('Error verificando token:', error);
      this.mostrarSesionExpirada();
      return;
    }
    this.usuario = JSON.parse(userFromLocal);
    console.log(this.usuario);
    // Iniciar carga en paralelo
    this.cargarUsuariosInicio();
    this.cargarForaneas();
  }
  private mostrarSesionExpirada(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');

    Swal.fire({
      title: 'Sesión expirada',
      text: 'Tu sesión ha caducado. Por favor, inicia sesión nuevamente.',
      icon: 'warning',
      confirmButtonText: 'Ir al login',
    }).then(() => {
      this.router.navigate(['/login']);
    });
  }
  private cargarUsuariosInicio(): void {
    const inicio = performance.now();

    this.usuariosService.obtenerUsuarios().subscribe({
      next: (data: Usuarios[]) => {
        this.usuarios = [];
        this.usuariosRolCinco = [];

        for (const u of data) {
          const rol =
            typeof u.rol === 'number'
              ? u.rol
              : typeof u.user?.rol === 'object'
              ? (u.user.rol as any)?.idRol
              : u.user?.rol;

          if (rol === 5) {
            this.usuariosRolCinco.push(u);
          } else {
            this.usuarios.push(u);
          }
        }

        this.totalPages = Math.ceil(this.usuarios.length / this.itemsPerPage);
        this.totalPagesExternos = Math.ceil(
          this.usuariosRolCinco.length / this.itemsPerPage
        );

        const fin = performance.now();
      },
      error: (err) => console.error('Error al cargar usuarios', err),
    });
  }

  mostrarInfoPorDocumento(numDocumento: string): void {
    this.usuariosService.obtenerUsuarioPorDocumento(numDocumento).subscribe({
      next: (res) => {
        this.usuarioSeleccionado = res.usuario;
        this.rolNombreSeleccionado =
          res.usuario.user?.rol?.nombreRol || 'Sin rol asignado';
        this.mostrarModal = true;
      },
      error: (err) => {
        console.error('Error al cargar usuario por documento', err);
      },
    });
  }

  mostrarInfoUsuario(usuarioId: any): void {
    // Buscar en ambas listas
    const usuarioCompleto =
      this.usuarios.find((u) => u.usersId === usuarioId) ||
      this.usuariosRolCinco.find((u) => u.usersId === usuarioId);

    if (usuarioCompleto) {
      this.usuarioSeleccionado = usuarioCompleto;

      this.usuariosService.obtenerUsersId(usuarioCompleto.usersId).subscribe({
        next: (response) => {
          const user = response[0]?.usuario;
          this.usuarioSeleccionado.user = user;

          const rolId =
            typeof user.rol === 'object' ? user.rol.idRol : user.rol;
          const rol = this.roles.find((r) => r.idRol === rolId);
          this.rolNombreSeleccionado = rol ? rol.nombreRol : 'Sin rol asignado';

          this.mostrarModal = true;
        },
        error: () => {
          console.error('Error al obtener los datos del usuario');
        },
      });
    } else {
      console.log('Usuario no encontrado:', usuarioId);
    }
  }

  cerrarModal(): void {
    this.mostrarModal = false;
    this.rolNombreSeleccionado = '';
  }

  actualizarRolUserBase(): void {
    if (!this.userBase?.id || !this.userBase?.rol) {
      Swal.fire('Error', 'Faltan datos para actualizar el rol.', 'error');
      return;
    }

    this.usuariosService
      .actualizarRolId(this.userBase.id, this.userBase.rol)
      .subscribe({
        next: () => {
          Swal.fire('Éxito', 'Rol actualizado correctamente.', 'success');
          this.cargarUsuarios(); // actualiza la tabla si quieres
        },
        error: (err) => {
          console.error('Error al actualizar rol:', err);
          Swal.fire('Error', 'No se pudo actualizar el rol.', 'error');
        },
      });
  }

  abrirModalAgregar(): void {
    this.nuevoUsuario = {
      primerNombre: '',
      segundoNombre: '',
      primerApellido: '',
      segundoApellido: '',
      telefono: '',
      email: '',
      email_confirmation: '',
      password: '',
      repetirPassword: '',
      direccion: '',
      numDocumento: '',
      nacionalidadId: null,
      epsCodigo: null,
      generoId: null,
      tipoDocumentoId: null,
      estadoCivilId: null,
      pensionesCodigo: null,
      rol: null,
      fechaNacimiento: '',
    };
    const modalElement = document.getElementById('agregarusuariosModal');
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    } else {
      console.error('No se encontró el modal con ID agregarusuariosModal');
    }
  }
  cargarRoles() {
    this.usuariosService.obtenerRoles().subscribe((data) => {
      this.roles = data;
    });
  }
  cambiarRol() {}
  cargarForaneas() {
    forkJoin({
      nacionalidades: this.usuariosService.obtenerNacionalidades().pipe(
        catchError((error) => {
          console.error('Error cargando nacionalidades:', error);
          return of([]);
        })
      ),
      generos: this.usuariosService.obtenerGeneros().pipe(
        catchError((error) => {
          console.error('Error cargando géneros:', error);
          return of([]);
        })
      ),
      tiposDocumento: this.usuariosService.obtenerTiposDocumento().pipe(
        catchError((error) => {
          console.error('Error cargando tipos documento:', error);
          return of([]);
        })
      ),
      estadosCiviles: this.usuariosService.obtenerEstadosCiviles().pipe(
        catchError((error) => {
          console.error('Error cargando estados civiles:', error);
          return of([]);
        })
      ),
      eps: this.usuariosService.obtenerEps().pipe(
        catchError((error) => {
          console.error('Error cargando EPS:', error);
          return of([]);
        })
      ),
      pensiones: this.usuariosService.obtenerPensiones().pipe(
        catchError((error) => {
          console.error('Error cargando pensiones:', error);
          return of([]);
        })
      ),
      roles: this.usuariosService.obtenerRoles().pipe(
        catchError((error) => {
          console.error('Error cargando roles:', error);
          return of([]);
        })
      ),
    }).subscribe({
      next: (res) => {
        this.nacionalidades = res.nacionalidades;
        this.generos = res.generos;
        this.tiposDocumento = res.tiposDocumento;
        this.estadosCiviles = res.estadosCiviles;
        this.eps = res.eps;
        this.pensiones = res.pensiones;
        this.roles = res.roles;
      },
      error: (err) => {
        console.error('Error general cargando datos foráneos:', err);
        Swal.fire(
          'Error',
          'No se pudieron cargar algunos datos del sistema',
          'warning'
        );
      },
    });
  }
  mostrarHojaVida(usuario: Usuarios): void {
    const inicio = performance.now();

    this.usuariosService.obtenerHojadevida(usuario.numDocumento).subscribe({
      next: (res) => {
        if (res && res.hojaDeVida) {
          this.hojaDeVidaSeleccionada = res.hojaDeVida;

          setTimeout(() => {
            this.abrirModalHojaVida();

            setTimeout(() => {
              const fin = performance.now();
            }, 200);
          }, 0);
        } else {
          this.hojaDeVidaSeleccionada = null;
          Swal.fire(
            'Atención',
            'No se encontró hoja de vida para el usuario.',
            'info'
          );
        }
      },
      error: (err) => {
        this.hojaDeVidaSeleccionada = null;
        Swal.fire(
          'Error',
          'No tiene asociada una hoja de vida para el usuario.',
          'error'
        );
      },
    });
  }

  trackByUsuario(index: number, usuario: Usuarios): number {
    return usuario.usersId;
  }
  trackById(index: number, item: any): number {
    return item.id;
  }

  cargarUsuarios(): void {
    this.usuariosService.obtenerUsuarios().subscribe({
      next: (data) => {
        this.usuarios = data;
        this.totalPages = Math.ceil(this.usuarios.length / this.itemsPerPage);

        // Precargar roles para cada usuario
        this.usuarios.forEach((usuario) => {
          if (usuario.usersId) {
            this.usuariosService.obtenerUsersId(usuario.usersId).subscribe({
              next: (userData) => {
                const idRol = userData?.rol;
                this.usuariosService.obtenerRolId(idRol).subscribe({
                  next: (rolData) => {
                    this.rolesPorUsuarioId[usuario.usersId] =
                      rolData?.nombreRol || 'Sin rol';
                  },
                  error: () => {
                    this.rolesPorUsuarioId[usuario.usersId] = 'Sin rol';
                  },
                });
              },
              error: () => {
                this.rolesPorUsuarioId[usuario.usersId] = 'Sin rol';
              },
            });
          }
        });
      },
      error: (err) => {
        console.error('Error al cargar usuarios', err);
      },
    });
  }

  nombreCompleto(usuario: any): string {
    if (!usuario) return '';
    return `${usuario.primerNombre} ${usuario.primerApellido}`;
  }

  editarusuarios(usuario: Usuarios, index: number): void {
    this.usuarioSeleccionado = { ...usuario };

    const roles$ = this.usuariosService.obtenerRoles();
    const user$ = this.usuariosService.obtenerUsersId(usuario.usersId);

    forkJoin([roles$, user$]).subscribe(([roles, user]) => {
      this.roles = roles;
      this.usuarioSeleccionado.rol =
        typeof user.rol === 'object' ? user.rol.idRol : Number(user.rol);

      const modalElement = document.getElementById('editarusuariosModal');
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
      }
    });
  }

  abrirModalHojaVida(): void {
    const modalElement = document.getElementById('hojaDeVidaModal');
    if (!modalElement) {
      console.error('No se encontró el modal de Hoja de Vida');
      return;
    }

    // Limpieza anticipada
    modalElement.removeAttribute('inert');
    (document.activeElement as HTMLElement)?.blur();

    const modal = new bootstrap.Modal(modalElement);

    modal.show();

    requestAnimationFrame(() => {
      if (modalElement.getAttribute('aria-hidden') === 'true') {
        console.warn(' Forzando eliminación de aria-hidden');
        modalElement.setAttribute('aria-hidden', 'false');
      }
    });
  }

  mostrarEstudios(usuario: Usuarios): void {
    console.log(`Mostrar Estudios de: ${usuario.numDocumento}`);
    // Lógica para abrir modal o redirigir
  }

  confirmDelete(usuario: Usuarios, index: number): void {
    Swal.fire({
      title: '¿Estás seguro?',
      text:
        'Esta acción eliminará al usuario de forma permanente ' +
        usuario.primerNombre +
        ' ' +
        usuario.primerApellido,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then((result) => {
      if (result.isConfirmed) {
        this.usuariosService.eliminarUsuario(usuario.numDocumento).subscribe({
          next: (res) => {
            Swal.fire('Eliminado', 'El usuario ha sido eliminado', 'success');
            this.cargarAmbasListasUsuarios();
          },
          error: (err) => {
            console.error(err);
            Swal.fire(
              'Error',
              'No se pudo eliminar el usuario Ya que tiene contratos asosiados',
              'error'
            );
          },
        });
      }
    });
  }

  rolesPorUsuario: { [userId: number]: string } = {};

  cargarRolesPorUsuarios(id: number): void {
    this.usuariosService.obtenerUsuario(id).subscribe({
      next: (usuario) => {
        console.log(usuario);
      },
      error: (err) => {
        console.error('Error al obtener el usuario:', err);
        this.rolesPorUsuario[id] = 'Sin rol';
      },
    });
  }
  cambiarRoles(usuario: any) {
    const nuevoRol = prompt(
      'Ingrese el nuevo ID de rol para este usuario:',
      usuario.rol
    );
    const idRol = parseInt(nuevoRol || '', 10);

    if (!isNaN(idRol)) {
      this.authService.actualizarRol(usuario.userId, idRol).subscribe({
        next: () => {
          Swal.fire('Actualizado', 'rol actualizado con éxito.', 'success');
          this.cargarUsuarios();
        },
        error: () => {
          Swal.fire('Error', 'No se pudo actualizar el rol.', 'error');
        },
      });
    }
  }
  cargarAmbasListasUsuarios(): void {
    this.usuariosService.obtenerUsuarios().subscribe({
      next: (data: Usuarios[]) => {
        this.usuarios = data.filter((u: Usuarios) => {
          const rol =
            typeof u.rol === 'number'
              ? u.rol
              : typeof u.user?.rol === 'object'
              ? (u.user.rol as any)?.idRol
              : u.user?.rol;
          return rol !== 5;
        });

        this.usuariosRolCinco = data.filter((u: Usuarios) => {
          const rol =
            typeof u.rol === 'number'
              ? u.rol
              : typeof u.user?.rol === 'object'
              ? (u.user.rol as any)?.idRol
              : u.user?.rol;
          return rol === 5;
        });

        this.totalPages = Math.ceil(this.usuarios.length / this.itemsPerPage);
        this.totalPagesExternos = Math.ceil(
          this.usuariosRolCinco.length / this.itemsPerPage
        );

        console.log('Usuarios normales:', this.usuarios);
        console.log('Usuarios externos:', this.usuariosRolCinco);
      },
      error: (err) =>
        console.error('Error al recargar ambas tablas de usuarios', err),
    });
  }

  get usuariosPaginados(): Usuarios[] {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.usuarios.slice(start, start + this.itemsPerPage);
  }

  cambiarPagina(pagina: number): void {
    if (pagina >= 1 && pagina <= this.totalPages) {
      this.currentPage = pagina;
    }
  }
  agregarUsuario(): void {
    const { email, numDocumento, password, repetirPassword, rol } =
      this.nuevoUsuario;

    if (password !== repetirPassword) {
      Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');
      return;
    }

    const inicio = performance.now();
    this.usuariosService
      .verificarExistenciaUsuario(email, numDocumento)
      .subscribe((existe: boolean) => {
        if (existe) {
          Swal.fire(
            'Error',
            'Ya existe un usuario con ese correo o número de documento.',
            'error'
          );
          return;
        }
        console.log('Resultado existencia:', existe);

        const userData = {
          name: `${this.nuevoUsuario.primerNombre} ${this.nuevoUsuario.primerApellido}`,
          email: email,
          email_confirmation: email,
          password: password,
          password_confirmation: repetirPassword,
          rol: Number(rol),
        };

        this.authService.register(userData).subscribe({
          next: (res) => {
            const userId = res.user?.id;

            const usuarioFinal = {
              numDocumento: this.nuevoUsuario.numDocumento,
              primerNombre: this.nuevoUsuario.primerNombre,
              segundoNombre: this.nuevoUsuario.segundoNombre,
              primerApellido: this.nuevoUsuario.primerApellido,
              segundoApellido: this.nuevoUsuario.segundoApellido,
              password: repetirPassword,
              fechaNac: this.nuevoUsuario.fechaNacimiento,
              numHijos: 0,
              contactoEmergencia: 'NO REGISTRADO',
              numContactoEmergencia: '0000000000',
              email: this.nuevoUsuario.email,
              direccion: this.nuevoUsuario.direccion,
              telefono: this.nuevoUsuario.telefono,
              nacionalidadId: Number(this.nuevoUsuario.nacionalidadId),
              epsCodigo: this.nuevoUsuario.epsCodigo,
              generoId: Number(this.nuevoUsuario.generoId),
              tipoDocumentoId: Number(this.nuevoUsuario.tipoDocumentoId),
              estadoCivilId: Number(this.nuevoUsuario.estadoCivilId),
              pensionesCodigo: this.nuevoUsuario.pensionesCodigo,
              usersId: userId,
            };

            this.usuariosService.agregarUsuario(usuarioFinal).subscribe({
              next: () => {
                const llegada = performance.now();
                console.log(
                  ` Backend respondió en: ${(llegada - inicio).toFixed(2)} ms`
                );

                // CERRAR EL MODAL PRIMERO
                this.cerrarModalAgregar();

                // MOSTRAR ALERTA DESPUÉS DE CERRAR EL MODAL
                Swal.fire({
                  title: '¡Éxito!',
                  text: 'El usuario fue creado correctamente.',
                  icon: 'success',
                  confirmButtonText: 'Aceptar',
                  timer: 3000,
                  timerProgressBar: true,
                });

                this.nuevoUsuario = {};
                this.cargarAmbasListasUsuarios();

                return;
              },
              error: (err) => {
                console.error('Error al guardar usuario:', err);
                if (err.status === 400 && err.error?.errors) {
                  const errores = Object.values(err.error.errors)
                    .flat()
                    .join('\n');
                  Swal.fire('Validación', errores, 'warning');
                } else {
                  Swal.fire('Error', 'No se pudo guardar el usuario.', 'error');
                }
              },
            });
          },
          error: (err) => {
            if (err.status === 422 && err.error?.errors) {
              const errores = Object.values(err.error.errors).flat().join(' ');
              Swal.fire('Error', errores, 'error');
            } else {
              Swal.fire(
                'Error',
                err.error?.message || 'No se pudo crear el usuario base.',
                'error'
              );
            }
          },
        });
      });
  }
  // Método para cerrar el modal de agregar usuario
cerrarModalAgregar(): void {
  const modalElement = document.getElementById('agregarusuariosModal');
  if (modalElement) {
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
      modal.hide();
    } else {
      // Si no hay instancia, crear una temporal para cerrarla
      const tempModal = new bootstrap.Modal(modalElement);
      tempModal.hide();
    }
  }
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

  actualizarUsuario(): void {
    if (!this.usuarioSeleccionado || !this.usuarioSeleccionado.numDocumento)
      return;

    this.usuariosService
      .actualizarUsuarioParcial(
        this.usuarioSeleccionado.numDocumento,
        this.usuarioSeleccionado
      )
      .subscribe({
        next: () => {
          // Luego de actualizar el usuario, actualiza el rol en la tabla 'users'
          this.usuariosService
            .actualizarRol(
              this.usuarioSeleccionado.usersId,
              this.usuarioSeleccionado.rol,
              this.usuarioSeleccionado.email
            )
            .subscribe({
              next: (res2) => {
                console.log('Respuesta del backend:', res2);

                // CERRAR EL MODAL DE EDICIÓN
                this.cerrarModalEditar();

                Swal.fire({
                  title: '¡Actualizado!',
                  text: 'El usuario fue editado exitosamente.',
                  icon: 'success',
                  confirmButtonText: 'Aceptar',
                }).then(() => {
                  this.cargarAmbasListasUsuarios();
                });
              },
              error: (err2) => {
                console.error('Error al actualizar el rol del usuario:', err2);
                Swal.fire(
                  'Error',
                  'No se pudo actualizar el rol del usuario.',
                  'error'
                );
              },
            });
        },
        error: (err) => {
          console.error('Error al actualizar usuario:', err);
          Swal.fire('Error', 'No se pudo actualizar el usuario.', 'error');
        },
      });
  }
  // Método para cerrar el modal de editar usuario
cerrarModalEditar(): void {
  const modalElement = document.getElementById('editarusuariosModal');
  if (modalElement) {
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
      modal.hide();
    } else {
      const tempModal = new bootstrap.Modal(modalElement);
      tempModal.hide();
    }
  }
}

  rolesPorUsuarioId: { [userId: number]: string } = {};

  cargarRolDeUsuario(idUser: number): void {
    console.log('Id obtenido ' + idUser);
    this.usuariosService.obtenerUsersId(idUser).subscribe({
      next: (usuarioId) => {
        console.log('ID= $ ' + usuarioId);
      },
    });
  }

  obtenerNombreRol(idRol: number): string {
    const rol = this.roles.find((r) => r.idRol === idRol);
    return rol ? rol.nombreRol : 'Sin rol';
  }

  currentPageExternos: number = 1;
  verExperiencia(usuario: any): void {
    this.usuariosService
      .obtenerExperienciaLaboral(usuario.numDocumento)
      .subscribe({
        next: (res) => {
          const hoja = res.data?.hojaDeVida;
          const experiencias = res.data?.experiencias;

          if (!hoja || !experiencias || experiencias.length === 0) {
            this.alerta(
              'Este usuario no tiene experiencias laborales registradas.'
            );
            this.usuarioSeleccionado = usuario;
            this.experienciasLaborales = []; // Vaciar para prevenir residuos

            return;
          }

          this.usuarioSeleccionado = res.data.usuario;
          this.hojaDeVidaSeleccionada = hoja;
          this.experienciasLaborales = experiencias;
          this.abrirModalExperiencia();
        },
        error: (err) => {
          if (
            err.status === 404 &&
            err.error?.mensaje === 'Hoja de vida no encontrada'
          ) {
            this.usuarioSeleccionado = usuario;
            this.experienciasLaborales = []; // Prevenir errores en el HTML
            this.abrirModalExperiencia();
          } else {
            console.error('Error inesperado al obtener experiencia:', err);
            this.alerta('Error inesperado al consultar experiencia.');
          }
        },
      });
  }

  abrirModalExperiencia(): void {
    const modal = document.getElementById('modalExperiencia');
    if (modal) {
      const modalInstance = new bootstrap.Modal(modal);
      modalInstance.show();
    }
  }
  alerta(mensaje: string): void {
    Swal.fire({
      icon: 'warning',
      title: 'Atención',
      text: mensaje,
      confirmButtonText: 'Cerrar',
    });
  }

  abrirModalErrorDatos(): void {
    const modal = document.getElementById('modalErrorDatos');
    if (modal) {
      const modalInstance = new bootstrap.Modal(modal);
      modalInstance.show();
    }
  }
  get totalPagesFiltrados(): number {
    const filtrados = this.usuarios.filter(
      (u) =>
        this.nombreCompleto(u)
          .toLowerCase()
          .includes(this.filtroNombre.toLowerCase()) ||
        u.numDocumento.toString().includes(this.filtroNombre)
    );
    return Math.ceil(filtrados.length / this.itemsPerPage);
  }
  get totalPagesExternosFiltrados(): number {
    const filtrados = this.usuariosRolCinco.filter(
      (u) =>
        this.nombreCompleto(u)
          .toLowerCase()
          .includes(this.filtroNombreExternos.toLowerCase()) ||
        u.numDocumento.toString().includes(this.filtroNombreExternos)
    );
    return Math.ceil(filtrados.length / this.itemsPerPage);
  }

  getPaginas(currentPage: number, totalPages: number): (number | string)[] {
    const delta = 2;
    const range: (number | string)[] = [];
    const left = Math.max(2, currentPage - delta);
    const right = Math.min(totalPages - 1, currentPage + delta);

    range.push(1);
    if (left > 2) range.push('...');

    for (let i = left; i <= right; i++) {
      range.push(i);
    }

    if (right < totalPages - 1) range.push('...');
    if (totalPages > 1) range.push(totalPages);

    return range;
  }

  irAPaginaUsuarios(pagina: number | string): void {
    if (typeof pagina === 'number') {
      this.cambiarPagina(pagina);
    }
  }

  irAPaginaExternos(page: number | string): void {
    if (typeof page === 'number') {
      this.currentPageExternos = page;
    }
  }
  estudios: any[] = [];

  mostrarHasEstudios(usuario: Usuarios): void {
    this.usuariosService.obtenerHasEstudios(usuario.numDocumento).subscribe({
      next: (res) => {
        const hoja = res.data?.hojaDeVida;
        const estudios = res.data?.estudios;

        if (!hoja || !estudios || estudios.length === 0) {
          this.alerta('Este usuario no tiene estudios registrados.');
          this.usuarioSeleccionado = usuario;
          this.estudios = [];
          return;
        }

        this.usuarioSeleccionado = res.data.usuario;
        this.hojaDeVidaSeleccionada = hoja;
        this.estudios = estudios;
        this.abrirModalEstudios();
      },
      error: (err) => {
        if (
          err.status === 404 &&
          err.error?.mensaje === 'Hoja de vida no encontrada'
        ) {
          this.usuarioSeleccionado = usuario;
          this.estudios = [];
          this.abrirModalEstudios();
        } else {
          console.error('Error al obtener estudios:', err);
          this.alerta('Error inesperado al consultar estudios.');
        }
      },
    });
  }
  abrirModalEstudios(): void {
    const modal = document.getElementById('modalEstudios');
    if (modal) {
      const modalInstance = new bootstrap.Modal(modal);
      modalInstance.show();
    } else {
      console.error('No se encontró el modal de Estudios');
    }
  }
  // Reemplazar el método urlEstudio existente
  urlEstudio(archivo: string): string {
    if (!archivo) return '';

    const baseUrl = 'https://www.evensoft21.com/managehr/api/public';

    // Si ya es una URL completa, devolver tal cual
    if (archivo.startsWith('http')) {
      return archivo;
    }

    // Si empieza con storage/, usar directamente
    if (archivo.startsWith('storage/')) {
      return `${baseUrl}/${archivo}`;
    }

    // Si es una ruta relativa, agregar storage/
    return `${baseUrl}/storage/${archivo.replace(/^\/?/, '')}`;
  }
  chart: any;
  generarGrafico(): void {
    this.usuariosService.getUsuariosConRoles().subscribe({
      next: (res) => {
        this.usuarios = res.usuario;

        const conteoPorRol: { [key: string]: number } = {};

        this.usuarios.forEach((usuario: any) => {
          const rolNombre = (usuario.user?.rol as any)?.nombreRol || 'Sin Rol';
          conteoPorRol[rolNombre] = (conteoPorRol[rolNombre] || 0) + 1;
        });

        const labels = Object.keys(conteoPorRol);
        const data = Object.values(conteoPorRol);

        if (this.chart) this.chart.destroy();

        this.chart = new Chart('graficaRoles', {
          type: 'bar',
          data: {
            labels,
            datasets: [
              {
                label: 'Usuarios por Rol',
                data,
                backgroundColor: [
                  '#4e73df',
                  '#1cc88a',
                  '#36b9cc',
                  '#f6c23e',
                  '#e74a3b',
                ],
              },
            ],
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: { display: true, text: 'Distribución de Roles' },
            },
          },
        });
      },
      error: (err) => {
        console.error('Error al obtener datos para el gráfico', err);
      },
    });
  }

  descargarExcel(): void {
    const workbook = new ExcelJS.Workbook();
    const sheet = workbook.addWorksheet('Usuarios');

    sheet.addRow([]); // espacio

    sheet.columns = [
      { header: 'Documento', key: 'documento', width: 20 },
      { header: 'Nombre', key: 'nombre', width: 30 },
      { header: 'Correo', key: 'correo', width: 30 },
    ];

    const usuariosPorRol: { [rol: string]: any[] } = {};
    this.usuarios.forEach((u) => {
      const rol = (u.user?.rol as any)?.nombreRol || 'Sin Rol';
      if (!usuariosPorRol[rol]) usuariosPorRol[rol] = [];
      usuariosPorRol[rol].push(u);
    });

    Object.entries(usuariosPorRol).forEach(([rol, usuarios]) => {
      const rolRow = sheet.addRow([`Rol: ${rol}`]);
      rolRow.font = { bold: true };
      rolRow.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FFD9D9D9' },
      };
      sheet.mergeCells(`A${rolRow.number}:C${rolRow.number}`);

      const encabezadoRow = sheet.addRow(['Documento', 'Nombre', 'Correo']);
      encabezadoRow.font = { bold: true };
      encabezadoRow.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FFB0C4DE' },
      };

      encabezadoRow.eachCell((cell) => {
        cell.border = {
          top: { style: 'thin' },
          bottom: { style: 'thin' },
          left: { style: 'thin' },
          right: { style: 'thin' },
        };
      });

      usuarios.forEach((u, index) => {
        const dataRow = sheet.addRow([
          u.numDocumento,
          `${u.primerNombre} ${u.primerApellido}`,
          u.email,
        ]);

        if (index % 2 === 0) {
          dataRow.fill = {
            type: 'pattern',
            pattern: 'solid',
            fgColor: { argb: 'FFF7F7F7' },
          };
        }

        dataRow.eachCell((cell) => {
          cell.border = {
            top: { style: 'thin' },
            bottom: { style: 'thin' },
            left: { style: 'thin' },
            right: { style: 'thin' },
          };
        });
      });

      sheet.addRow([]);
    });

    workbook.xlsx.writeBuffer().then((buffer) => {
      const blob = new Blob([buffer], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      });
      saveAs(blob, 'usuarios_reporte.xlsx');
    });
  }

  descargarPDF(): void {
    const doc = new jsPDF();

    const imgLogo = new Image();
    imgLogo.src = 'https://i.postimg.cc/BnHG09W1/logo.png';

    imgLogo.onload = () => {
      doc.setFillColor(4, 26, 43);
      doc.rect(0, 0, 210, 30, 'F');
      doc.addImage(imgLogo, 'PNG', 10, 5, 20, 20);
      doc.setFontSize(16);
      doc.setTextColor(200);
      doc.text('ManageHR - Sistema para gestión de recursos humanos', 35, 15);

      let startY = 35;

      const canvas: any = document.getElementById('graficaRoles');
      const graficaImg = canvas.toDataURL('image/png', 1.0);
      doc.addImage(graficaImg, 'PNG', 10, startY, 180, 80);

      startY += 90;

      const usuariosPorRol: { [rol: string]: any[] } = {};
      this.usuarios.forEach((u) => {
        const rol = (u.user?.rol as any)?.nombreRol || 'Sin Rol';
        if (!usuariosPorRol[rol]) usuariosPorRol[rol] = [];
        usuariosPorRol[rol].push(u);
      });

      Object.entries(usuariosPorRol).forEach(([rol, usuarios]) => {
        doc.setFontSize(12);
        doc.setTextColor(0);
        doc.text(`Rol: ${rol}`, 10, startY);

        const body = usuarios.map((u) => [
          u.numDocumento,
          `${u.primerNombre} ${u.primerApellido}`,
          u.email,
        ]);

        autoTable(doc, {
          head: [['Documento', 'Nombre', 'Correo']],
          body,
          startY: startY + 5,
          theme: 'grid',
          styles: {
            halign: 'left',
            fontSize: 10,
          },
          didParseCell: (data) => {
            if (data.section === 'body' && data.row.index % 2 === 0) {
              data.cell.styles.fillColor = [240, 240, 240];
            }
          },
        });

        startY = (doc as any).lastAutoTable.finalY + 10;
      });

      doc.save('usuarios_reporte.pdf');
    };
  }

  cerrarModalReporte(): void {
    const modalElement = document.getElementById('modalReporteUsuarios');
    if (modalElement) {
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) {
        modalInstance.hide();
      } else {
        console.warn(
          'No se encontró una instancia activa del modal. Creando una nueva para cerrarla.'
        );
        new bootstrap.Modal(modalElement).hide();
      }
    } else {
      console.error(
        'No se encontró el elemento modalReporteUsuarios en el DOM'
      );
    }
  }

  abrirModalReporte(): void {
    const modalEl = document.getElementById('modalReporteUsuarios');
    if (modalEl) {
      const modalInstance = new bootstrap.Modal(modalEl);
      modalInstance.show();
    }

    this.generarGrafico();
  }
  // Método para abrir cualquier archivo en el modal
  // Método para abrir cualquier archivo en el modal
  abrirArchivoModal(archivo: string): void {
    if (!archivo) {
      this.mostrarError('No se especificó ningún archivo');
      return;
    }

    // Cerrar todos los modales abiertos primero
    this.cerrarTodosLosModales();

    this.cargandoArchivo = true;
    this.errorArchivo = '';
    this.archivoActual = this.urlEstudio(archivo);

    console.log('🔗 Abriendo archivo:', this.archivoActual);

    // Pequeño delay para asegurar que los modales anteriores se cierren
    setTimeout(() => {
      const modalEl = document.getElementById('modalVerArchivo');
      if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Limpiar estados cuando se cierre el modal
        modalEl.addEventListener('hidden.bs.modal', () => {
          this.limpiarEstadoArchivo();
        });
      }
    }, 300); // Aumentamos el delay para dar tiempo a cerrar modales anteriores
  }

  // Método para cerrar todos los modales abiertos
  cerrarTodosLosModales(): void {
    // Cerrar todos los modales de Bootstrap
    const modales = document.querySelectorAll('.modal');
    modales.forEach((modal: any) => {
      const modalInstance = bootstrap.Modal.getInstance(modal);
      if (modalInstance) {
        modalInstance.hide();
      }
    });

    // Remover backdrop si existe
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
      backdrop.remove();
    }

    // Restaurar el body
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  }

  // Método cuando el archivo se carga correctamente
  onArchivoCargado(): void {
    this.cargandoArchivo = false;
    this.errorArchivo = '';
    console.log('✅ Archivo cargado correctamente');
  }

  // Método cuando hay error cargando el archivo
  onErrorArchivo(): void {
    this.cargandoArchivo = false;
    this.errorArchivo =
      'No se pudo cargar el archivo. Puede que no exista o no sea accesible.';
    console.error('❌ Error cargando archivo:', this.archivoActual);
  }

  // Descargar archivo
  descargarArchivo(): void {
    if (!this.archivoActual) return;

    const link = document.createElement('a');
    link.href = this.archivoActual;
    link.target = '_blank';
    link.download = this.obtenerNombreArchivo(this.archivoActual);

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    this.mostrarExito('Descarga iniciada');
  }

  // Helper: Obtener extensión del archivo
  obtenerExtensionArchivo(url: string): string {
    return url.split('.').pop()?.toLowerCase() || 'desconocido';
  }

  // Helper: Obtener nombre del archivo
  obtenerNombreArchivo(url: string): string {
    return url.split('/').pop() || 'archivo';
  }

  // Verificar si es PDF
  esPDF(url: string): boolean {
    return this.obtenerExtensionArchivo(url) === 'pdf';
  }

  // Verificar si es imagen
  esImagen(url: string): boolean {
    const extensiones = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    return extensiones.includes(this.obtenerExtensionArchivo(url));
  }

  // Limpiar estado
  private limpiarEstadoArchivo(): void {
    this.archivoActual = '';
    this.cargandoArchivo = false;
    this.errorArchivo = '';
  }

  private mostrarError(mensaje: string): void {
    console.error('Error:', mensaje);
    // Puedes usar SweetAlert2 si prefieres
    alert(mensaje);
  }

  private mostrarExito(mensaje: string): void {
    console.log('Éxito:', mensaje);
  }
  // Método para descarga directa sin abrir el modal
  descargarArchivoDirecto(archivo: string): void {
    const url = this.urlEstudio(archivo);
    const link = document.createElement('a');
    link.href = url;
    link.target = '_blank';
    link.download = this.obtenerNombreArchivo(archivo);

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    this.mostrarExito('Descarga iniciada');
  }

  // Cerrar modal específico
  cerrarModalPorId(modalId: string): void {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) {
        modalInstance.hide();
      } else {
        // Si no hay instancia, crear una temporal para cerrarla
        const tempModal = new bootstrap.Modal(modalElement);
        tempModal.hide();
      }
    }
  }
  // Método simple para abrir en nueva pestaña
  abrirEnNuevaPestaña(): void {
    if (!this.archivoActual) return;
    window.open(this.archivoActual, '_blank');
  }
  // Método mejorado para abrir archivos desde modales
  abrirArchivoDesdeModal(archivo: string, modalIdActual?: string): void {
    if (!archivo) {
      Swal.fire('Error', 'No se especificó ningún archivo', 'error');
      return;
    }

    console.log('📁 Archivo a abrir:', archivo);

    // 1. Cerrar modal actual de forma correcta
    if (modalIdActual) {
      this.cerrarModalCorrectamente(modalIdActual);
    }

    // 2. Esperar a que se cierre completamente el modal anterior
    setTimeout(() => {
      this.prepararYMostrarArchivo(archivo);
    }, 500);
  }

  // Cerrar modal de forma correcta
  cerrarModalCorrectamente(modalId: string): void {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
      // Remover atributos problemáticos
      modalElement.removeAttribute('aria-hidden');
      modalElement.style.display = 'none';

      // Cerrar con Bootstrap
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) {
        modalInstance.hide();
      } else {
        const tempModal = new bootstrap.Modal(modalElement);
        tempModal.hide();
      }

      // Limpiar backdrop
      this.limpiarBackdrop();
    }
  }

  // Limpiar backdrop de Bootstrap
  limpiarBackdrop(): void {
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach((backdrop) => {
      backdrop.remove();
    });

    // Restaurar el body
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  }
  // Preparar y mostrar el archivo de forma correcta
  prepararYMostrarArchivo(archivo: string): void {
    this.cargandoArchivo = true;
    this.errorArchivo = '';
    this.archivoActual = this.urlEstudio(archivo);

    console.log('🔗 URL del archivo generada:', this.archivoActual);

    // Pequeño delay para asegurar que todo esté listo
    setTimeout(() => {
      const modalEl = document.getElementById('modalVerArchivo');
      if (modalEl) {
        // Asegurarse de que el modal esté limpio
        modalEl.removeAttribute('aria-hidden');
        modalEl.style.display = 'block';

        const modal = new bootstrap.Modal(modalEl, {
          backdrop: 'static',
          keyboard: true,
        });

        modal.show();

        // Manejar eventos correctamente
        modalEl.addEventListener('shown.bs.modal', () => {
          console.log('✅ Modal de archivo mostrado correctamente');
        });

        modalEl.addEventListener('hidden.bs.modal', () => {
          this.limpiarEstadoArchivo();
          this.limpiarBackdrop();
        });
      } else {
        console.error('❌ No se encontró el modal modalVerArchivo');
        this.cargandoArchivo = false;
        this.errorArchivo = 'No se pudo abrir el visor de archivos';
      }
    }, 300);
  }
}
