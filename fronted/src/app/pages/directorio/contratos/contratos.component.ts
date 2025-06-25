import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ContratosService, Contratos } from '../../../services/contratos.service';
import { UsuariosService, Usuarios } from '../../../services/usuarios.service';
import { AuthService } from '../../../services/auth.service';
import { MenuComponent } from '../../menu/menu.component';
import Swal from 'sweetalert2';
import { Route } from '@angular/router';
import { NgxPaginationModule } from 'ngx-pagination';
import { FilterNombre } from './filter-nombre';
declare var bootstrap: any;

@Component({
  selector: 'app-contratos',
  standalone: true,
  imports: [CommonModule, FormsModule, MenuComponent, NgxPaginationModule, FilterNombre],
  templateUrl: './contratos.component.html',
  styleUrls: ['./contratos.component.scss']
})

export class ContratosComponent implements OnInit {
  contratos: Contratos[] = [];
  filtroNombre: string = "";
  currentPage = 1;
  itemsPerPage = 5;
  nacionalidades: any[] = [];
  tipoContratoId: any[] = [];
  numDocumento: any[] = [];
  totalPages = 5;
  archivoSeleccionado: File | null = null;
  tiposContrato: any[] = [];
  areas: any[] = [];
  contratoSeleccionado: Contratos = {
  idContrato: 0,
  tipoContratoId: 1,
  estado: 1,
  fechaIngreso: '',
  fechaFinalizacion: '',
  archivo: '',
  cargoArea: 1, // <-- agrega esta línea
  area: {
    idArea: 0,
    nombreArea: ''
  },
  hoja_de_vida: {
    idHojaDeVida: 0,
    usuarioNumDocumento: 0,
    usuario: {
      idUsuario: 0,
      numDocumento: 0,
      primerNombre: '',
      primerApellido: ''
    }
  },
  tipo_contrato: {
    idTipoContrato: 0,
    nomTipoContrato: ''
  }
};




  contrato: any = {}; // contrato logueado desde localStorage
  usuario: any = {};
  nuevocontrato: any = {};

  constructor(private contratosService: ContratosService, private usuariosService: UsuariosService) { }

  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('usuario logueado:', this.usuario);
    }

    this.contratosService.obtenerContratos().subscribe({
      next: (data) => {
        console.log('contrato cargado:', data);
        this.contratos = data;
        this.totalPages = Math.ceil(this.contratos.length / this.itemsPerPage);
      },
      error: (err) => {
        console.error('Error al obtener contratos:', err);
      }
    });
    this.contratosService.obtenerAreas().subscribe({
      next: res => {
        console.log('Áreas cargadas:', res); 
        this.areas = res;
      },
      error: () => Swal.fire('Error', 'No se pudieron cargar las áreas', 'error')
    });

    this.obtenerTiposContrato();
  }
  cargoNombre(cargoAreaId?: number): string {
    const cargos: { [key: number]: string } = {
      1: 'Empleado',
      2: 'Jefe de personal',
      3: 'Coordinador',
      4: 'Director'
    };
    return cargos[cargoAreaId ?? 0] || 'Desconocido';
  }

  obtenerTiposContrato(): void {
    this.contratosService.obtenerTiposContrato().subscribe({
      next: (data) => {
        this.tiposContrato = data;
        console.log('Tipos de documento cargados:', this.tiposContrato);
      },
      error: (error) => {
        console.error('Error al cargar tipos de documento', error);
      }
    });
  }

  onFileSelected(event: any): void {
    this.archivoSeleccionado = event.target.files[0];
  }

  cargarContratos(): void {
    this.contratosService.obtenerContratos().subscribe({
      next: (data) => {
        this.contratos = data;
        console.log('contratos cargados:', this.contratos);
      },
      error: (err) => {
        console.error('Error al cargar contratos', err);
      }
    });
  }

  editarContrato(contrato: any, i: number) {
    this.contratoSeleccionado = { ...contrato };
  }

  confirmDelete(idContrato: number): void {
    // Buscar el contrato en el array
    const contrato = this.contratos.find(c => c.idContrato === idContrato);

    if (!contrato) {
      Swal.fire('Error', 'Contrato no encontrado.', 'error');
      return;
    }

    if (!contrato.hoja_de_vida || !contrato.hoja_de_vida.usuario) {
      Swal.fire('Error', 'No se encontró información del usuario.', 'error');
      return;
    }

    const nombre = `${contrato.hoja_de_vida.usuario.primerNombre} ${contrato.hoja_de_vida.usuario.primerApellido}`;

    Swal.fire({
      title: `¿Eliminar a ${nombre}?`,
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.contratosService.eliminarContrato(idContrato).subscribe({
          next: () => {
            Swal.fire({
              title: 'Eliminado',
              text: `${nombre} fue eliminado correctamente.`,
              icon: 'success',
              confirmButtonText: 'Aceptar'
            }).then(() => {
              this.cargarContratos(); // O refresca solo la lista
            });
          },
          error: (err) => {
            console.error('Error al eliminar:', err);
            Swal.fire('Error', 'No se pudo eliminar el contrato.', 'error');
          }
        });
      }
    });
  }


  get contratosPaginados(): Contratos[] {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    return this.contratosFiltrados.slice(start, start + this.itemsPerPage);
  }

  cambiarPagina(pagina: number): void {
    if (pagina >= 1 && pagina <= this.totalPages) {
      this.currentPage = pagina;
    }
  }

  agregarContrato(): void {
  // Validar antes de enviar
  if (!this.contratoSeleccionado.hoja_de_vida.usuarioNumDocumento || !this.contratoSeleccionado.tipoContratoId) {
    Swal.fire('Error', 'Faltan datos obligatorios', 'error');
    return;
  }

  const formData = new FormData();
  formData.append('numDocumento', this.contratoSeleccionado.hoja_de_vida.usuarioNumDocumento.toString());
  formData.append('tipoContratoId', this.contratoSeleccionado.tipoContratoId.toString());
  formData.append('estado', this.contratoSeleccionado.estado.toString());
  formData.append('fechaIngreso', this.contratoSeleccionado.fechaIngreso);
  formData.append('fechaFinalizacion', this.contratoSeleccionado.fechaFinalizacion);
  formData.append('area', this.contratoSeleccionado.area.idArea.toString());
  formData.append('cargoArea', this.contratoSeleccionado.cargoArea?.toString() || '1');

  if (this.archivoSeleccionado) {
    formData.append('archivo', this.archivoSeleccionado);
  } else {
    Swal.fire('Error', 'Debe adjuntar un archivo', 'error');
    return;
  }

  this.contratosService.agregarContrato(formData).subscribe({
    next: (res) => {
      Swal.fire({
        title: '¡Éxito!',
        text: 'El contrato fue creado exitosamente.',
        icon: 'success',
        confirmButtonText: 'Aceptar'
      }).then(() => {
       
        this.cargarContratos();
       
      });
    },
    error: (err) => {
      console.error('Error al crear contrato:', err);
      Swal.fire('Error', 'No se pudo crear el contrato. Verifique los datos.', 'error');
    }
  });
}


  imagenSeleccionada: string = '';
  abrirModalImagen(url: string | null) {
    if (!url) {
      console.warn('No hay archivo para mostrar');
      Swal.fire('Advertencia', 'No hay documento asociado para mostrar.', 'warning');
      return;
    }
    
    this.imagenSeleccionada = 'http://localhost:8000/' + url;
    setTimeout(() => {
      const modalElement = document.getElementById('modalImagen');
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
      } else {
        console.error('Modal no encontrado en el DOM');
      }
    }, 200);
  }

  actualizarContrato(): void {
    const formData = new FormData();
    formData.append('_method', 'PATCH');
    formData.append('numDocumento', this.contratoSeleccionado.hoja_de_vida.usuario.numDocumento.toString());
    formData.append('tipoContratoId', this.contratoSeleccionado.tipoContratoId.toString());
    formData.append('estado', this.contratoSeleccionado.estado.toString());
    formData.append('fechaIngreso', this.contratoSeleccionado.fechaIngreso);
    formData.append('fechaFinalizacion', this.contratoSeleccionado.fechaFinalizacion);
    formData.append('area', this.contratoSeleccionado.area.idArea.toString());
    formData.append('cargoArea', this.contratoSeleccionado.cargoArea?.toString() || '1');

    if (this.archivoSeleccionado) {
      formData.append('archivo', this.archivoSeleccionado);
    }

    console.log('ID que se está enviando:', this.contratoSeleccionado.idContrato);

    this.contratosService.actualizarContratoParcial(this.contratoSeleccionado.idContrato, formData).subscribe({
      next: (res) => {
        Swal.fire({
          title: '¡Actualizado!',
          text: 'El contrato fue editado exitosamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar'
        }).then(() => {
          location.reload();
        });

        const index = this.contratos.findIndex(c => c.hoja_de_vida.usuario.idUsuario === this.contratoSeleccionado.hoja_de_vida.usuario.idUsuario);
        if (index !== -1) {
          this.contratos[index] = { ...this.contratoSeleccionado };
        }
      },
      error: (err) => {
        console.error('Error al actualizar contrato:', err);
        Swal.fire({
          title: '¡Error!',
          text: 'Algo salió mal, no se pudo actualizar.',
          icon: 'error',
          confirmButtonText: 'Aceptar'
        }).then(() => {
          location.reload();
        });
      }
    });
  }

  abrirModalAgregar(): void {
    this.nuevocontrato = {
      numDocumento: '',
      tipoContratoId: '',
      estado: '',
      fechaIngreso: '',
      fechaFinal: '',
      documento: '',
      area: { idArea: 0, nombreArea: '' }
    };
  }

  getNombreTipoContrato(tipoContratoId: number): string {
    const tipo = this.tiposContrato.find(t => t.idTipoContrato === tipoContratoId);
    return tipo ? tipo.nomTipoContrato : 'Desconocido';
  }

  getNombreEstado(estado: number): string {
    switch (estado) {
      case 1: return 'Activo';
      case 2: return 'Bloqueado';
      case 3: return 'Cancelado';
      default: return 'Desconocido';
    }
  }

  getClaseEstado(estado: number): string {
    switch (estado) {
      case 1: return 'badge bg-success';
      case 2: return 'badge bg-warning text-dark';
      case 3: return 'badge bg-danger';
      default: return 'badge bg-secondary';
    }
  }

  get contratosFiltrados(): Contratos[] {
    if (!this.filtroNombre.trim()) return this.contratos;
    const filtro = this.filtroNombre.toLowerCase();
    return this.contratos.filter(c => {
      const nombreUsuario = `${c.hoja_de_vida.usuario.primerNombre} ${c.hoja_de_vida.usuario.primerApellido}`.toLowerCase();
      return nombreUsuario.includes(filtro);
    });
  }
  esImagen(archivo: string): boolean {
    return archivo.match(/\.(jpg|jpeg|png|gif)$/i) !== null;
  }

  esPDF(archivo: string): boolean {
    return archivo.match(/\.pdf$/i) !== null;
  }

  esExcel(archivo: string): boolean {
    return archivo.match(/\.(xls|xlsx)$/i) !== null;
  }

  esOtro(archivo: string): boolean {
    return !this.esImagen(archivo) && !this.esPDF(archivo) && !this.esExcel(archivo);
  }

}
