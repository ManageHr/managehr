import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { UsuariosService, Usuarios } from '../../../services/usuarios.service';
import { AuthService } from '../../../services/auth.service';
import { MenuComponent } from '../../menu/menu.component';
import Swal from 'sweetalert2';
import { Route } from '@angular/router';
import { NgxPaginationModule } from 'ngx-pagination';
import { FilterNombre } from './filter-nombre';
declare var bootstrap: any;

@Component({
  selector: 'app-usuarios',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent, NgxPaginationModule, FilterNombre],
  templateUrl: './usuarios.component.html',
  styleUrls: ['./usuarios.component.scss']
})
export class UsuariosComponent implements OnInit {
  agregarusuariosModal = false;
  usuarios: Usuarios[] = [];
  userBase: any = null;

  usuariosRolCinco: Usuarios[] = [];
  hojaDeVidaSeleccionada: any = null;

  filtroNombre: string = "";
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
    { idRol: 7, nombreRol: 'Para borrar nuevo MODEL' }
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
    password: "",
    password_confirmation: "",
    direccion: '',
    numDocumento: 0,
    nacionalidadId: 0,
    epsCodigo: "",
    generoId: 0,
    tipoDocumentoId: 0,
    estadoCivilId: 0,
    pensionesCodigo: "",
    rol: 0,
    usersId: 0
  };

  usuario: any = {};
  nuevoUsuario: any = {};
  constructor(private usuariosService: UsuariosService, private authService: AuthService) { }

  totalPagesExternos: number = 1;

  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('Usuario logueado:', this.usuario);
    }

    // Esta parte es solo para cargar todos los usuarios si aún la usas
    this.usuariosService.obtenerUsuarios().subscribe({
      next: (data) => {
        this.usuarios = data;
        this.totalPages = Math.ceil(this.usuarios.length / this.itemsPerPage);
      },
      error: (err) => console.error('Error al cargar usuarios', err)
    });

    // ESTA es la parte importante para rol 5
    this.usuariosService.getUsuariosRolCinco().subscribe({
      next: (data) => {
        this.usuariosRolCinco = data;
        this.totalPagesExternos = Math.ceil(this.usuariosRolCinco.length / this.itemsPerPage);
        console.log('Usuarios con rol 5:', this.usuariosRolCinco); // 👈 debe mostrar datos
      },
      error: (err) => console.error('Error al cargar usuarios rol 5', err)
    });







    this.cargarForaneas();

  }
  mostrarInfoPorDocumento(numDocumento: string): void {
    this.usuariosService.obtenerUsuarioPorDocumento(numDocumento).subscribe({
      next: (res) => {
        this.usuarioSeleccionado = res.usuario;
        this.rolNombreSeleccionado = res.usuario.user?.rol?.nombreRol || 'Sin rol asignado';
        this.mostrarModal = true;
      },
      error: (err) => {
        console.error('Error al cargar usuario por documento', err);
      }
    });
  }


  mostrarInfoUsuario(usuarioId: any): void {
    const usuarioCompleto = this.usuarios.find(u => u.usersId === usuarioId);
    console.log('Usuario encontrado:', usuarioCompleto);
    if (usuarioCompleto) {
      this.usuarioSeleccionado = usuarioCompleto;

      this.usuariosService.obtenerUsersId(usuarioCompleto.usersId).subscribe({
        next: (response) => {
          const user = response[0]?.usuario;
          this.usuarioSeleccionado.user = user;
          console.log('Usuario recibido:', user);
          console.log('Roles cargados:', this.roles);
          console.log('Rol ID recibido:', user.rol?.idRol);

          const rolId = typeof user.rol === 'object' ? user.rol.idRol : user.rol;
          const rol = this.roles.find(r => r.idRol === rolId);
          this.rolNombreSeleccionado = rol ? rol.nombreRol : 'Sin rol asignado';

          this.mostrarModal = true;
        },
        error: () => {
          console.error('Error al obtener los datos del usuario');
        }
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

    this.usuariosService.actualizarRolId(this.userBase.id, this.userBase.rol).subscribe({
      next: () => {
        Swal.fire('Éxito', 'Rol actualizado correctamente.', 'success');
        this.cargarUsuarios(); // actualiza la tabla si quieres
      },
      error: (err) => {
        console.error('Error al actualizar rol:', err);
        Swal.fire('Error', 'No se pudo actualizar el rol.', 'error');
      }
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
      password: "",
      repetirPassword: "",
      direccion: '',
      numDocumento: 0,
      nacionalidadId: null,
      epsCodigo: null,
      generoId: null,
      tipoDocumentoId: null,
      estadoCivilId: null,
      pensionesCodigo: null,
      rol: null
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
    this.usuariosService.obtenerRoles().subscribe(data => {
      this.roles = data;
    });
  }
  cambiarRol() {

  }
  cargarForaneas() {
    this.usuariosService.obtenerNacionalidades().subscribe(data => this.nacionalidades = data);
    this.usuariosService.obtenerGeneros().subscribe(data => this.generos = data);
    this.usuariosService.obtenerTiposDocumento().subscribe(data => this.tiposDocumento = data);
    this.usuariosService.obtenerEstadosCiviles().subscribe(data => this.estadosCiviles = data);
    this.usuariosService.obtenerEps().subscribe(data => {
      this.eps = data;
      console.log('EPS cargadas:', data);
    });

    this.usuariosService.obtenerPensiones().subscribe(data => {
      this.pensiones = data;
      console.log('Pensiones cargadas:', data);
    });
    this.usuariosService.obtenerRoles().subscribe(data => {
      this.roles = data;
      console.log('Roles cargados:', this.roles);
    });

  }
  mostrarHojaVida(usuario: Usuarios): void {
    console.log("usuario de HV ", usuario.numDocumento);
    this.usuariosService.obtenerHojadevida(usuario.numDocumento).subscribe({
      next: (res) => {
        console.log('Respuesta Hoja de Vida:', res);
        if (res && res.hojaDeVida) {
          this.hojaDeVidaSeleccionada = res.hojaDeVida;
          console.log(this.hojaDeVidaSeleccionada);
        } else {
          this.hojaDeVidaSeleccionada = null;
          Swal.fire('Atención', 'No se encontró hoja de vida para el usuario.', 'info');
        }
        this.abrirModalHojaVida();
      },
      error: (err) => {
        console.error('Error al obtener la hoja de vida:', err);
        Swal.fire('Error', 'No tiene asociada una hoja de vida para el usuario.', 'error');
      }
    });
  }

  cargarUsuarios(): void {
    this.usuariosService.obtenerUsuarios().subscribe({
      next: (data) => {
        this.usuarios = data;
        this.totalPages = Math.ceil(this.usuarios.length / this.itemsPerPage);

        // Precargar roles para cada usuario
        this.usuarios.forEach(usuario => {
          if (usuario.usersId) {
            this.usuariosService.obtenerUsersId(usuario.usersId).subscribe({
              next: (userData) => {
                const idRol = userData?.rol;
                this.usuariosService.obtenerRolId(idRol).subscribe({
                  next: (rolData) => {
                    this.rolesPorUsuarioId[usuario.usersId] = rolData?.nombreRol || 'Sin rol';
                  },
                  error: () => {
                    this.rolesPorUsuarioId[usuario.usersId] = 'Sin rol';
                  }
                });
              },
              error: () => {
                this.rolesPorUsuarioId[usuario.usersId] = 'Sin rol';
              }
            });
          }
        });

      },
      error: (err) => {
        console.error('Error al cargar usuarios', err);
      }
    });
  }


  nombreCompleto(usuario: any): string {
    if (!usuario) return '';
    return `${usuario.primerNombre} ${usuario.primerApellido}`;
  }

  editarusuarios(usuario: Usuarios, index: number): void {
  this.usuarioSeleccionado = { ...usuario };

  this.usuariosService.obtenerUsersId(usuario.usersId).subscribe(user => {
    // Asegurarte que usuarioSeleccionado.rol tenga el id del rol
    this.usuarioSeleccionado.rol = typeof user.rol === 'object' ? user.rol.idRol : user.rol;

    const modalElement = document.getElementById('editarusuariosModal');
    if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
    }
  });
}


abrirModalHojaVida(): void {
  const modalElement = document.getElementById('hojaDeVidaModal');
  if (modalElement) {
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
  } else {
    console.error('No se encontró el modal de Hoja de Vida');
  }
}




mostrarEstudios(usuario: Usuarios): void {
  console.log(`Mostrar Estudios de: ${usuario.numDocumento}`);
  // Lógica para abrir modal o redirigir
}





  confirmDelete(index: number): void {
    const usuario = this.usuariosPaginados[index];
    const nombre = `${usuario.primerNombre} ${usuario.primerApellido}`;

    Swal.fire({
      title: `¿Eliminar a ${nombre}?`,
      text: 'Esta acción eliminará también el acceso del usuario.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Primero elimina el usuario extendido (tabla Usuarios)
        this.usuariosService.eliminarUsuario(usuario.numDocumento).subscribe({
          next: () => {
            // Luego elimina el acceso base (tabla Users)
            this.usuariosService.eliminarUser(usuario.usersId).subscribe({
              next: () => {
                Swal.fire('Eliminado', 'Usuario eliminado correctamente.', 'success');
                this.cargarUsuarios();
              },
              error: (err) => {
                console.error('Error al eliminar acceso base:', err);
                Swal.fire('Error', 'No se pudo eliminar el acceso base del usuario.', 'error');
              }
            });
          },
          error: (err) => {
            console.error('Error al eliminar usuario extendido:', err);
            Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error');
          }
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
      }
    });
  }
  cambiarRoles(usuario: any) {
    const nuevoRol = prompt('Ingrese el nuevo ID de rol para este usuario:', usuario.rol);
    const idRol = parseInt(nuevoRol || '', 10);

    if (!isNaN(idRol)) {
      this.authService.actualizarRol(usuario.userId, idRol).subscribe({
        next: () => {
          Swal.fire('Actualizado', 'rol actualizado con éxito.', 'success');
          this.cargarUsuarios();
        },
        error: () => {
          Swal.fire('Error', 'No se pudo actualizar el rol.', 'error');
        }
      });
    }
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
    const { email, numDocumento, password, repetirPassword, rol } = this.nuevoUsuario;

    if (password !== repetirPassword) {
      Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');
      return;
    }

    // 1. Validar en tabla usuarios si ya existe correo o documento
    this.usuariosService.verificarExistenciaUsuario(email, numDocumento).subscribe((existe: boolean) => {
      if (existe) {
        Swal.fire('Error', 'Ya existe un usuario con ese correo o número de documento.', 'error');
        return;
      }
      console.log('Resultado existencia:', existe);

      // 2. Crear primero el user
      const userData = {
        name: `${this.nuevoUsuario.primerNombre} ${this.nuevoUsuario.primerApellido}`,
        email: email,
        email_confirmation: email,            // 👈 necesario
        password: password,
        password_confirmation: repetirPassword,
        rol: Number(rol)
      };
      console.log('Datos que se envían a /register:', userData);
      this.authService.register(userData).subscribe({
        next: (res) => {
          const userId = res.user?.id;

          const usuarioFinal = {
            numDocumento: Number(this.nuevoUsuario.numDocumento),
            primerNombre: this.nuevoUsuario.primerNombre,
            segundoNombre: this.nuevoUsuario.segundoNombre,
            primerApellido: this.nuevoUsuario.primerApellido,
            segundoApellido: this.nuevoUsuario.segundoApellido,
            password: repetirPassword,
            fechaNac: '2000-01-01',
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
              Swal.fire('¡Éxito!', 'El usuario fue creado correctamente.', 'success');
              this.nuevoUsuario = {};
              this.cargarUsuarios();
            },
            error: (err) => {
              console.error('Error al guardar usuario:', err);
              if (err.status === 400 && err.error?.errors) {
                const errores = Object.values(err.error.errors).flat().join('\n');
                Swal.fire('Validación', errores, 'warning');
              } else {
                Swal.fire('Error', 'No se pudo guardar el usuario.', 'error');
              }
            }
          });
        },
        error: (err) => {
          if (err.status === 422 && err.error?.errors) {
            const errores = Object.values(err.error.errors).flat().join(' ');
            Swal.fire('Error', errores, 'error');
          } else {
            Swal.fire('Error', err.error?.message || 'No se pudo crear el usuario base.', 'error');
          }
        }
      });
    });
  }
  
  actualizarUsuario(): void {
    if (!this.usuarioSeleccionado || !this.usuarioSeleccionado.numDocumento) return;

    this.usuariosService.actualizarUsuarioParcial(
      this.usuarioSeleccionado.numDocumento,
      this.usuarioSeleccionado
    ).subscribe({
      next: () => {
        // Luego de actualizar el usuario, actualiza el rol en la tabla 'users'
        this.usuariosService.actualizarRol(this.usuarioSeleccionado.usersId, this.usuarioSeleccionado.rol, this.usuarioSeleccionado.email).subscribe({
          next: (res2) => {
            console.log('Respuesta del backend:', res2);
            Swal.fire({
              title: '¡Actualizado!',
              text: 'El usuario fue editado exitosamente.',
              icon: 'success',
              confirmButtonText: 'Aceptar'
            }).then(() => location.reload());
          },
          error: (err2) => {
            console.error('Error al actualizar el rol del usuario:', err2);
            Swal.fire('Error', 'No se pudo actualizar el rol del usuario.', 'error');
          }
        });
      },
      error: (err) => {
        console.error('Error al actualizar usuario:', err);
        Swal.fire('Error', 'No se pudo actualizar el usuario.', 'error');
      }
    });
  }

  rolesPorUsuarioId: { [userId: number]: string } = {};

  cargarRolDeUsuario(idUser: number): void {
    console.log("Id obtenido " + idUser);
    this.usuariosService.obtenerUsersId(idUser).subscribe({
      next: (usuarioId) => {
        console.log("ID= $ " + usuarioId);
      },

    });
  }

  obtenerNombreRol(idRol: number): string {
    const rol = this.roles.find(r => r.idRol === idRol);
    return rol ? rol.nombreRol : 'Sin rol';
  }

  currentPageExternos: number = 1;
  verExperiencia(usuario: any): void {
    this.usuariosService.obtenerExperienciaLaboral(usuario.numDocumento).subscribe({
      next: (res) => {
        this.usuarioSeleccionado = res.data.usuario;
        this.hojaDeVidaSeleccionada = res.data.hojaDeVida;
        this.experienciasLaborales = res.data.experiencias;
        this.abrirModalExperiencia();
      },
      error: (err) => {
        console.error('Error al obtener experiencia:', err);
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



}
