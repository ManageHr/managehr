export interface User {
    primerNombre: string;
    segundoNombre?: string; // Agregado para evitar el error
    primerApellido: string;
    segundoApellido?: string;
    usuario: string;
    tipoDocumento: string;
    numeroDocumento: string;
    fechaNacimiento: string;
    numeroHijos?: number | null; // Ahora permite null
    contactoEmergencia?: string;
    numeroContactoEmergencia?: string;
    email: string;
    direccion: string;
    telefono: string;
    nacionalidad?: string;
    eps?: string;
    genero: string;
    estadoCivil?: string;
    pensiones?: string;
  }
  