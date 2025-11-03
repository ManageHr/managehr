export interface SolicitudVacacionesJefe {
  idVacaciones?: number;
  motivo: string;
  fechaInicio: string;
  fechaFinal: string;
  dias: number;
  contratoId: number;
  estado?: 'pendiente' | 'aprobado' | 'rechazado';

  empleado?: {
    numDocumento: number;
    nombre: string;
    apellido: string;
  };

  contrato?: {
    id: number;
    tipoContrato: string;
    fechaInicio: string;
    fechaFin?: string;
  };
 
  createdAt?: string;
  updatedAt?: string;
}

export interface RespuestaSolicitud {
  idVacaciones: number;
  estado: 'aprobado' | 'rechazado';
  comentario?: string;
}
