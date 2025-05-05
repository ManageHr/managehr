import { Pipe, PipeTransform } from '@angular/core';
import { ContratosService, Contratos} from '../../../services/contratos.service';

@Pipe({
  name: 'filterNombre',
  standalone: true
})
export class FilterNombre implements PipeTransform {
  transform(contratos: Contratos[], termino: string): Contratos[] {
    if (!contratos || !termino) return contratos;

    const lowerFiltro = termino.toLowerCase();
    return contratos.filter(contrato =>
      `${contrato.fechaIngreso} ${contrato.fechaFinal} ${contrato.numDocumento}`
        .toLowerCase()
        .includes(lowerFiltro)
    );
  }
}
