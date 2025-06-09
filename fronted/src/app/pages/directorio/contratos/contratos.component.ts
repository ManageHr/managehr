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
  imports: [CommonModule, FormsModule, MenuComponent,NgxPaginationModule,FilterNombre],
  templateUrl: './contratos.component.html',
  styleUrls: ['./contratos.component.scss']
})

export class ContratosComponent implements OnInit{
  contratos: Contratos[] = [];
  filtroNombre: string ="";
  currentPage = 1;
  itemsPerPage = 5;
  nacionalidades: any[] = [];
  tipoContratoId: any[] = [];
  numDocumento: any[] = [];
  totalPages=5;
  archivoSeleccionado: File | null = null;
  tiposContrato: any[] = [];
  

  contratoSeleccionado: Contratos = {
    idContrato: 0,
    numDocumento: 0, 
    tipoContratoId: 1,
    estado: 1,
    fechaIngreso: '',
    fechaFinalizacion: '',
    archivo: '',
    area: 0,
    hojasDeVida:0,
  };

  contrato: any = {}; // contrato logueado desde localStorage
  usuario:any={};
  nuevocontrato: any = {};
  area:any={};


  constructor(private contratosService: ContratosService,private usuariosService: UsuariosService) {}

  ngOnInit(): void {
    const userFromLocal = localStorage.getItem('usuario');
    if (userFromLocal) {
      this.usuario = JSON.parse(userFromLocal);
      console.log('usuario logueado:', this.usuario);
    }

   this.contratosService.obtenerContratos().subscribe({
    next: (data) => {
      console.log('contrato cargado:', data); // Ahora mostrará el array con 3 elementos
      this.contratos = data;
      this.totalPages = Math.ceil(this.contratos.length / this.itemsPerPage);
    },
    error: (err) => {
      console.error('Error al obtener contratos:', err);
    }
  });




    this.obtenerTiposContrato();
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
  confirmDelete(index: number): void {
    const contrato = this.contratos[index];
  console.log(contrato);
    this.usuariosService.obtenerUsuario(contrato.numDocumento).subscribe({
      next: (res) => {
        const usuario = res.usuario ?? res;
  
        if (!usuario || !usuario.primerNombre || !usuario.primerApellido) {
          Swal.fire('Error', 'No se encontró información del usuario.', 'error');
          return;
        }
  
        const nombre = `${usuario.primerNombre} ${usuario.primerApellido}`;
  
        Swal.fire({
          title: `¿Eliminar a ${nombre}?`,
          text: 'Esta acción no se puede deshacer.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            this.contratosService.eliminarContrato(contrato.idContrato).subscribe({
              next: () => {
                Swal.fire({
                  title: 'Eliminado',
                  text: `${nombre} fue eliminado correctamente.`,
                  icon: 'success',
                  confirmButtonText: 'Aceptar'
                }).then(() => {
                  location.reload();
                });
              },
              error: (err) => {
                console.error('Error al eliminar:', err);
                Swal.fire('Error', 'No se pudo eliminar el contrato.', 'error');
              }
            });
          }
        });
      },
      error: (err) => {
        console.error('No se pudo obtener el usuario:', err);
        Swal.fire('Error', 'No se pudo obtener la información del usuario.', 'error');
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
    this.usuariosService.obtenerUsuario(this.contratoSeleccionado.numDocumento).subscribe({
      next: (res) => {
        const usuario = res.usuario ?? res;
  
        if (!usuario || !usuario.primerNombre || !usuario.primerApellido) {
          Swal.fire('Error', 'No se encontró información del usuario.', 'error');
          return;
        }
  
        const nombre = `${usuario.primerNombre} ${usuario.primerApellido}`;
  
        // Si el usuario existe, sigue con el guardado
        const formData = new FormData();
        formData.append('numDocumento', this.contratoSeleccionado.numDocumento.toString());
        formData.append('tipoContratoId', this.contratoSeleccionado.tipoContratoId.toString());
        formData.append('estado', this.contratoSeleccionado.estado.toString());
        formData.append('fechaIngreso', this.contratoSeleccionado.fechaIngreso);
        formData.append('fechaFinal', this.contratoSeleccionado.fechaFinalizacion);
        formData.append('areaId', this.contratoSeleccionado.area.toString());
  
        if (this.archivoSeleccionado) {
          formData.append('documento', this.archivoSeleccionado);
        }
  
        this.contratosService.agregarContrato(formData).subscribe({
          next: () => {
            Swal.fire({
              title: '¡OK!',
              text: `El contrato para ${nombre} fue creado exitosamente.`,
              icon: 'success',
              confirmButtonText: 'Aceptar'
            }).then(() => {
              location.reload(); // o this.cargarContratos() si no quieres recargar
            });
          },
          error: (err) => {
            console.error('Error al guardar:', err);
            Swal.fire('¡ERROR!', 'No se pudo crear el contrato.', 'error');
          }
        });
      },
      error: (err) => {
        console.error('Error al buscar el usuario:', err);
        Swal.fire('Error', 'No se encontró el usuario. Verifique el número de documento.', 'error');
      }
    });
  }
  
  
  imagenSeleccionada: string = '';
   

  abrirModalImagen(url: string) {
    this.imagenSeleccionada = 'http://localhost:8000/' + url;

    setTimeout(() => {
      const modalElement = document.getElementById('modalImagen');
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        document.getElementById('modalImagen');

      } else {
        console.error('Modal no encontrado en el DOM');
      }
    }, 200); // <-- prueba con 200ms
  }

  actualizarContrato(): void {

    const formData = new FormData();
  
    // Campos obligatorios o editables
    formData.append('_method', 'PATCH');
    
    formData.append('numDocumento', this.contratoSeleccionado.numDocumento.toString());
    formData.append('tipoContratoId', this.contratoSeleccionado.tipoContratoId.toString());
    formData.append('estado', this.contratoSeleccionado.estado.toString());
    formData.append('fechaIngreso', this.contratoSeleccionado.fechaIngreso);
    formData.append('fechaFinalizacion', this.contratoSeleccionado.fechaFinalizacion);
    formData.append('area', this.contratoSeleccionado.area.toString());
    // Archivo (si fue seleccionado)
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
          location.reload(); // o this.cargarContratos(); si no querés recargar toda la página
        });
  
        const index = this.contratos.findIndex(c => c.numDocumento === this.contratoSeleccionado.numDocumento);
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
          location.reload(); // o this.cargarContratos(); si no querés recargar toda la página
        })
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
      areaId:0
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
    return this.contratos.filter(c =>
  (c?.nombreUsuario ?? '').toLowerCase().includes(filtro)
);



  }



  
  
  
  
}
