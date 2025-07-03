import { Pipe, PipeTransform } from '@angular/core';
import { Usuarios } from '../../../services/usuarios.service';

@Pipe({
  name: 'filterNombre',
  standalone: true
})
export class FilterNombre implements PipeTransform {
  transform(usuarios: Usuarios[], filtro: string): Usuarios[] {
    if (!usuarios || !filtro) return usuarios;

    const filtroLower = filtro.toLowerCase();

    return usuarios.filter(u => {
      const nombreCompleto = `${u.primerNombre} ${u.segundoNombre || ''} ${u.primerApellido} ${u.segundoApellido || ''}`.toLowerCase();
      const documento = String(u.numDocumento);
      return (
        nombreCompleto.includes(filtroLower) ||
        documento.includes(filtroLower)
      );
    });
  }
}
