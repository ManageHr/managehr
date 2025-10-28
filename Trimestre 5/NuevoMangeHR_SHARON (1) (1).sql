drop database if exists manageHr ;
create database manageHr;
use manageHr;

create table usuarios (
	numDocumento int,
    primerNombre varchar(30) not null,
    segundoNombre varchar(30),
    primerApellido varchar(30) not null,
    segundoApellido varchar(30),
	clave varbinary(200) not null,
    fechaNac date not null,
    numHijos tinyint(2),
    contactoEmergencia varchar(30) not null,
    numContactoEmergencia int not null,
    correo varchar(45) not null,
    direccion varchar(45) not null,
    telefono int not null,
    nacionalidadId int not null,
    epsCodigo char(6) not null,
    generoId int not null,
    tipoDocumentoId int not null,
    estadoCivilId int not null,
    pensionesCodigo char(6) not null
);

create table tipoContrato (
	idTipoContrato int,
    nomTipoContrato varchar(45) not null
);

create table contrato (
	idContrato int,
    estado tinyint not null,
    fechaIngreso date not null,
    fechaFinal date,
	documento varchar(100) not null,
	tipoContratoId int not null,
    numDocumento int not null
);

create table area (
	idArea int,
    jefePersonal varchar(100) not null,
    estado tinyint not null,
    contratoId int not null,
    postulacionesId int not null
);

create table hojasVida (
	idHojaDeVida int,
    claseLibretaMilitar varchar(45) not null,
    numeroLibretaMilitar varchar(45) not null,
    usuarioNumDocumento int not null
);

create table estudios (
	idEstudios int,
	nomEstudio varchar(45) not null,
    nomInstitucion varchar(50) not null,
    tituloObtenido varchar(45) not null, 
    añoFinalizacion year not null
);

create table experienciaLaboral (
	idExperiencia int,
    nomEmpresa varchar(45) not null,
    nombJefe varchar(45) not null,
    telefono int not null,
    cargo varchar(20) not null,
    actividades TEXT(500) not null,
    certificado varchar(100) not null,
    fechaInicio date not null,
    fechaFin date not null
);

create table tipoDocumento (
	idTipoDocumento int,
	nombreTipoDocumento varchar (50) not null,
    abreviacionTipoDocumento char(5) not null
);

create table genero (
	idGenero int,
	nombreGenero varchar(15) not null,
    abreviacionGenero char(1) not null
);

create table estadoCivil (
	idEstadoCivil int,
    nombreEstado varchar(20) not null
);

create table rol (
	idRol int,
    nombreRol varchar(45) not null
);

create table tipoPermiso (
	idTipoPermiso int,
    nomTipoPermiso varchar(30) not null
);


create table permisos (
	idPermiso int,
    descrip text(1000) not null,
    fechaInicio date not null,
    fechaFinal date,
    estado tinyint not null,
    tipoPermisoId int not null,
    contratoId int not null
);


create table pazYSalvo (
	idPazSalvo int,
    descrip text(500) not null,
    firma varchar(45) not null,
    contratoId int not null
);

create table incapacidad (
	idIncapacidad int,
    descrip text(500) not null,
    archivo varchar(45) not null,
    fechaInicio date not null,
    fechaFinal date not null,
    contratoId int not null
);

create table vacaciones (
	idVacaciones int,
    descrip text(500) not null,
    archivo varchar(45) not null,
    fechaInicio date not null,
    fechaFinal date not null,
    contratoId int not null
);

create table horasExtra (
	idHorasExtra int,
    descrip text(500) not null,
    fecha date not null,
    nHorasExtra int not null,
    tipoHorasid int not null,
    contratoId int not null
);

create table tipoHoras (
	idTipoHoras int,
    nombreTipoHoras varchar(10) not null
);

create table pensiones (
	codigoPensiones char(6),
    nombrePensiones varchar(40) not null
);

create table eps (
	codigoEps char(6),
    nombreEps varchar(40) not null
);

create table nacionalidad (
	idNacionalidad int,
    nombre varchar(50) not null
);

create table categoriaVacantes (
	idCatVac int,
    nomCategoria varchar(45) not null
);

create table vacantes (
	idVacantes int,
    nomVacante varchar(30) not null,
    descripVacante text(500) not null,
    salario float not null,
    expMinima varchar(45) not null,
    cargoVacante varchar(45) not null,
    catVacId int not null
);

create table postulaciones (
	idPostulaciones int,
    fechaPostulacion date not null,
    estado tinyint not null,
    vacantesId int not null
);



create table hojaVidaHasEstudio(
	estadoEstudio char(10) not null,
    hojaVidaId int not null,
    estudiosId int not null
);

create table hojaVidaHasExperienciaLaboral(
	estadoExperiencia char(10) not null,
    hojaVidaId int not null,
    experienciaLaboralId int not null
);

create table usuariosHasRol (
	estado char(10) not null,
    usuarioNumDocumento int not null,
    rolId int
);

create table categoriaVacantesHasUsuario (
	categoriaVacantesId int not null, 
    usuarioNumDocumento int not null
);

create table categoriaVacantesHasVacantes (
	estadocategoria tinyint not null,
	categoriaVacantesId int not null, 
    vacantesid int not null
);

create table vacantesHasPostulaciones (
	vacantesid int not null,
    postulacionesid int not null
);

create table trazabilidad (
	idTrazabilidad bigint null primary key auto_increment,
    fechaModificacion datetime not null,
    iP varchar(100) not null,
    usuarioanterior varchar(100) not null,
    usuarionuevo varchar(100) not null,
    claveAnterior varbinary(200) not null,
    claveNueva varbinary(200) not null,
    numDocumento int not null
);


alter table area
	add constraint pk_area primary key (idArea);

alter table tipoContrato 
	add constraint pk_tipoContrato primary key (idTipoContrato);
    
alter table contrato 
	add constraint pk_contrato primary key (idContrato);
    
alter table tipoPermiso 
	add constraint pk_tipoPermiso primary key (idTipoPermiso);
    
alter table permisos 
	add constraint pk_permisos primary key (idPermiso);
    
alter table nacionalidad 
	add constraint pk_nacionalidad primary key (idNacionalidad);
    
alter table eps
	add constraint pk_eps primary key (codigoEps);
    
alter table pensiones
	add constraint pk_pensiones primary key (codigoPensiones);
    
alter table Rol
	add constraint pk_rol  primary key auto_increment(idRol);
    
alter table usuarios
	add constraint pk_usuario primary key (numDocumento);
    
alter table hojasVida
	add constraint pk_hojasDeVida primary key (idHojaDeVida);
    
alter table estudios
	add constraint pk_estudios primary key (idEstudios);

alter table tipohoras
	add constraint pk_tipoHoras primary key (idTipoHoras);
    
alter table experienciaLaboral
	add constraint pk_experienciaLaboral primary key (idExperiencia);

alter table pazYSalvo
	add constraint pk_pazYSalvo primary key (idPazSalvo);
    
alter table incapacidad
	add constraint pk_incapacidad primary key (idIncapacidad);
    
alter table vacaciones
	add constraint pk_vacaciones primary key (idVacaciones);
    
alter table horasExtra
	add constraint pk_horasExtra primary key (idHorasExtra);
    
alter table categoriaVacantes
	add constraint pk_categoriaVacantes primary key (idCatVac);
    
alter table Vacantes
	add constraint pk_Vacantes primary key (idVacantes);
    
alter table postulaciones
	add constraint pk_Postulaciones primary key (idPostulaciones);
    
alter table estadocivil
	add constraint pk_EstadoCivil primary key (idEstadoCivil);
    
alter table tipodocumento
	add constraint pk_TipoDocumento primary key (idTipoDocumento);
    
alter table genero
	add constraint pk_Genero primary key (idGenero);


ALTER TABLE area MODIFY idArea INT NOT NULL AUTO_INCREMENT;


ALTER TABLE tipoContrato MODIFY idTipoContrato INT NOT NULL AUTO_INCREMENT;


ALTER TABLE tipoPermiso MODIFY idTipoPermiso INT NOT NULL AUTO_INCREMENT;


ALTER TABLE permisos MODIFY idPermiso INT NOT NULL AUTO_INCREMENT;


ALTER TABLE hojasVida MODIFY idHojaDeVida INT NOT NULL AUTO_INCREMENT;


ALTER TABLE estudios MODIFY idEstudios INT NOT NULL AUTO_INCREMENT;


ALTER TABLE experienciaLaboral MODIFY idExperiencia INT NOT NULL AUTO_INCREMENT;


ALTER TABLE tipoDocumento MODIFY idTipoDocumento INT NOT NULL AUTO_INCREMENT;


ALTER TABLE genero MODIFY idGenero INT NOT NULL AUTO_INCREMENT;


ALTER TABLE estadoCivil MODIFY idEstadoCivil INT NOT NULL AUTO_INCREMENT;


ALTER TABLE rol MODIFY idRol INT NOT NULL AUTO_INCREMENT;


ALTER TABLE pazYSalvo MODIFY idPazSalvo INT NOT NULL AUTO_INCREMENT;


ALTER TABLE incapacidad MODIFY idIncapacidad INT NOT NULL AUTO_INCREMENT;


ALTER TABLE vacaciones MODIFY idVacaciones INT NOT NULL AUTO_INCREMENT;


ALTER TABLE horasExtra MODIFY idHorasExtra INT NOT NULL AUTO_INCREMENT;


ALTER TABLE tipoHoras MODIFY idTipoHoras INT NOT NULL AUTO_INCREMENT;


ALTER TABLE nacionalidad MODIFY idNacionalidad INT NOT NULL AUTO_INCREMENT;


ALTER TABLE nacionalidad ADD COLUMN codigo INT NOT NULL AFTER idNacionalidad;

ALTER TABLE categoriaVacantes MODIFY idCatVac INT NOT NULL AUTO_INCREMENT;


ALTER TABLE vacantes MODIFY idVacantes INT NOT NULL AUTO_INCREMENT;


ALTER TABLE postulaciones MODIFY idPostulaciones INT NOT NULL AUTO_INCREMENT;

alter table usuariosHasRol
	add constraint fk_usuarios_rol foreign key (usuarioNumDocumento) references usuarios(numDocumento),
	add constraint fk_rol_usuarios foreign key (rolId) references rol(idRol);

alter table hojavidahasestudio
	add constraint fk_hojavida_estudio foreign key (hojaVidaId) references hojasvida(idHojaDeVida),
	add constraint fk_estudio_hojavida foreign key (estudiosId) references estudios(idEstudios);
    
alter table hojavidahasexperiencialaboral
	add constraint fk_hojavida_experiencia foreign key (hojaVidaId) references hojasvida(idHojaDeVida),
	add constraint fk_experiencia_hojavida foreign key (experienciaLaboralId) references experiencialaboral(idExperiencia);
    
alter table categoriavacanteshasusuario
	add constraint fk_categoriavacantes_usuarios foreign key (categoriaVacantesId) references categoriavacantes(idCatVac),
	add constraint fk_usuarios_categoriavacantes foreign key (usuarioNumDocumento) references usuarios(numDocumento);
    
alter table categoriavacanteshasvacantes
	add constraint fk_categoriavacantes_vacantes foreign key (categoriaVacantesId) references categoriavacantes(idCatVac),
	add constraint fk_vacantes_categoriavacantes foreign key (vacantesid) references vacantes(idVacantes);
    
alter table usuarios
	add constraint fk_usuario_estadocivil foreign key (estadoCivilId) references estadocivil(idEstadoCivil),
    add constraint fk_usuario_tipodedocumento foreign key (tipoDocumentoId) references tipodocumento(idTipoDocumento),
	add constraint fk_usuario_genero foreign key (generoId) references genero(idGenero),
    add constraint fk_usuario_nacionalidad foreign key (nacionalidadId) references nacionalidad(idNacionalidad),
    add constraint fk_usuario_eps foreign key (epsCodigo) references eps(codigoEps),
    add constraint fk_usuario_pensiones foreign key (pensionesCodigo) references pensiones(codigoPensiones);
    
alter table hojasvida
	add constraint fk_hojasvida_usuarios foreign key (usuarioNumDocumento) references usuarios(numDocumento);
    
alter table horasextra
	add constraint fk_horasextra_tipohoras foreign key (tipoHorasid) references tipohoras(idTipoHoras),
    add constraint fk_contratoId_horasextra foreign key (contratoId) references contrato(idContrato);
    
alter table vacaciones
    add constraint fk_ContratoId_vacaciones foreign key (ContratoId) references Contrato(idContrato);
    
alter table incapacidad
    add constraint fk_ContratoId_incapacidad foreign key (ContratoId) references Contrato(idContrato);
    
alter table pazYSalvo
    add constraint fk_ContratoId_pazYSalvo foreign key (ContratoId) references Contrato(idContrato);
    
alter table contrato 
	add constraint fk_tipoContratoId_contrato foreign key (tipoContratoId) references tipoContrato(idTipoContrato),
    add constraint fk_numDocumento_contrato foreign key (numDocumento) references usuarios(numDocumento);
    
alter table area
	add constraint fk_contratoId_area foreign key (contratoId) references contrato(idContrato),
    add constraint fk_postulacionesId_area foreign key (postulacionesId) references postulaciones(IdPostulaciones);
   
alter table permisos
	add constraint fk_tipoPermiso_permisos foreign key (tipoPermisoId) references tipoPermiso(idTipoPermiso),
    add constraint fk_ContratoId_permisos foreign key (ContratoId) references Contrato(idContrato);
    
    
-- Vistas --

CREATE VIEW v_mostrarTodosLosUsuarios AS

SELECT * FROM usuarios;

SELECT * FROM v_mostrarTodosLosUsuarios;

insert into nacionalidad values(null,102,"AUSTRIA");
insert into nacionalidad values(null,103,"BELGICA");
insert into nacionalidad values(null,104,"BULGARIA");
insert into nacionalidad values(null,106,"CHIPRE");
insert into nacionalidad values(null,107,"DINAMARCA");
insert into nacionalidad values(null,108,"ESPAÑA");
insert into nacionalidad values(null,109,"FINLANDIA");
insert into nacionalidad values(null,110,"FRANCIA");
insert into nacionalidad values(null,111,"GRECIA");
insert into nacionalidad values(null,112,"HUNGRIA");
insert into nacionalidad values(null,113,"IRLANDA");
insert into nacionalidad values(null,115,"ITALIA");
insert into nacionalidad values(null,117,"LUXEMBURGO");
insert into nacionalidad values(null,118,"MALTA");
insert into nacionalidad values(null,121,"PAISES BAJOS");
insert into nacionalidad values(null,122,"POLONIA");
insert into nacionalidad values(null,123,"PORTUGAL");
insert into nacionalidad values(null,125,"REINO UNIDO");
insert into nacionalidad values(null,126,"ALEMANIA");
insert into nacionalidad values(null,128,"RUMANIA");
insert into nacionalidad values(null,131,"SUECIA");
insert into nacionalidad values(null,136,"LETONIA");
insert into nacionalidad values(null,141,"ESTONIA");
insert into nacionalidad values(null,142,"LITUANIA");
insert into nacionalidad values(null,143,"REPUBLICA CHECA");
insert into nacionalidad values(null,144,"REPUBLICA ESLOVACA");
insert into nacionalidad values(null,147,"ESLOVENIA");
insert into nacionalidad values(null,198,"OTROS PAISES O TERRITORIOS DE LA UNION EUROPEA");
insert into nacionalidad values(null,101,"ALBANIA");
insert into nacionalidad values(null,114,"ISLANDIA");
insert into nacionalidad values(null,116,"LIECHTENSTEIN");
insert into nacionalidad values(null,119,"MONACO");
insert into nacionalidad values(null,120,"NORUEGA");
insert into nacionalidad values(null,124,"ANDORRA");
insert into nacionalidad values(null,129,"SAN MARINO");
insert into nacionalidad values(null,130,"SANTA SEDE");
insert into nacionalidad values(null,132,"SUIZA");
insert into nacionalidad values(null,135,"UCRANIA");
insert into nacionalidad values(null,137,"MOLDAVIA");
insert into nacionalidad values(null,138,"BELARUS");
insert into nacionalidad values(null,139,"GEORGIA");
insert into nacionalidad values(null,145,"BOSNIA Y HERZEGOVINA");
insert into nacionalidad values(null,146,"CROACIA");
insert into nacionalidad values(null,148,"ARMENIA");
insert into nacionalidad values(null,154,"RUSIA");
insert into nacionalidad values(null,156,"MACEDONIA ");
insert into nacionalidad values(null,157,"SERBIA");
insert into nacionalidad values(null,158,"MONTENEGRO");
insert into nacionalidad values(null,170,"GUERNESEY");
insert into nacionalidad values(null,171,"SVALBARD Y JAN MAYEN");
insert into nacionalidad values(null,172,"ISLAS FEROE");
insert into nacionalidad values(null,173,"ISLA DE MAN");
insert into nacionalidad values(null,174,"GIBRALTAR");
insert into nacionalidad values(null,175,"ISLAS DEL CANAL");
insert into nacionalidad values(null,176,"JERSEY");
insert into nacionalidad values(null,177,"ISLAS ALAND");
insert into nacionalidad values(null,436,"TURQUIA");
insert into nacionalidad values(null,199,"OTROS PAISES O TERRITORIOS DEL RESTO DE EUROPA");
insert into nacionalidad values(null,201,"BURKINA FASO");
insert into nacionalidad values(null,202,"ANGOLA");
insert into nacionalidad values(null,203,"ARGELIA");
insert into nacionalidad values(null,204,"BENIN");
insert into nacionalidad values(null,205,"BOTSWANA");
insert into nacionalidad values(null,206,"BURUNDI");
insert into nacionalidad values(null,207,"CABO VERDE");
insert into nacionalidad values(null,208,"CAMERUN");
insert into nacionalidad values(null,209,"COMORES");
insert into nacionalidad values(null,210,"CONGO");
insert into nacionalidad values(null,211,"COSTA DE MARFIL");
insert into nacionalidad values(null,212,"DJIBOUTI");
insert into nacionalidad values(null,213,"EGIPTO");
insert into nacionalidad values(null,214,"ETIOPIA");
insert into nacionalidad values(null,215,"GABON");
insert into nacionalidad values(null,216,"GAMBIA");
insert into nacionalidad values(null,217,"GHANA");
insert into nacionalidad values(null,218,"GUINEA");
insert into nacionalidad values(null,219,"GUINEA-BISSAU");
insert into nacionalidad values(null,220,"GUINEA ECUATORIAL");
insert into nacionalidad values(null,221,"KENIA");
insert into nacionalidad values(null,222,"LESOTHO");
insert into nacionalidad values(null,223,"LIBERIA");
insert into nacionalidad values(null,224,"LIBIA");
insert into nacionalidad values(null,225,"MADAGASCAR");
insert into nacionalidad values(null,226,"MALAWI");
insert into nacionalidad values(null,227,"MALI");
insert into nacionalidad values(null,228,"MARRUECOS");
insert into nacionalidad values(null,229,"MAURICIO");
insert into nacionalidad values(null,230,"MAURITANIA");
insert into nacionalidad values(null,231,"MOZAMBIQUE");
insert into nacionalidad values(null,232,"NAMIBIA");
insert into nacionalidad values(null,233,"NIGER");
insert into nacionalidad values(null,234,"NIGERIA");
insert into nacionalidad values(null,235,"REPUBLICA CENTROAFRICANA");
insert into nacionalidad values(null,236,"SUDAFRICA");
insert into nacionalidad values(null,237,"RUANDA");
insert into nacionalidad values(null,238,"SANTO TOME Y PRINCIPE");
insert into nacionalidad values(null,239,"SENEGAL");
insert into nacionalidad values(null,240,"SEYCHELLES");
insert into nacionalidad values(null,241,"SIERRA LEONA");
insert into nacionalidad values(null,242,"SOMALIA");
insert into nacionalidad values(null,243,"SUDAN");
insert into nacionalidad values(null,244,"SWAZILANDIA");
insert into nacionalidad values(null,245,"TANZANIA");
insert into nacionalidad values(null,246,"CHAD");
insert into nacionalidad values(null,247,"TOGO");
insert into nacionalidad values(null,248,"TUNEZ");
insert into nacionalidad values(null,249,"UGANDA");
insert into nacionalidad values(null,250,"REP.DEMOCRATICA DEL CONGO");
insert into nacionalidad values(null,251,"ZAMBIA");
insert into nacionalidad values(null,252,"ZIMBABWE");
insert into nacionalidad values(null,253,"ERITREA");
insert into nacionalidad values(null,260,"SANTA HELENA");
insert into nacionalidad values(null,261,"REUNION");
insert into nacionalidad values(null,262,"MAYOTTE");
insert into nacionalidad values(null,263,"SAHARA OCCIDENTAL");
insert into nacionalidad values(null,299,"OTROS PAISES O TERRITORIOS DE AFRICA");
insert into nacionalidad values(null,301,"CANADA");
insert into nacionalidad values(null,302,"ESTADOS UNIDOS DE AMERICA");
insert into nacionalidad values(null,303,"MEXICO");
insert into nacionalidad values(null,370,"SAN PEDRO Y MIQUELON ");
insert into nacionalidad values(null,371,"GROENLANDIA");
insert into nacionalidad values(null,396,"OTROS PAISES  O TERRITORIOS DE AMERICA DEL NORTE");
insert into nacionalidad values(null,310,"ANTIGUA Y BARBUDA");
insert into nacionalidad values(null,311,"BAHAMAS");
insert into nacionalidad values(null,312,"BARBADOS");
insert into nacionalidad values(null,313,"BELICE");
insert into nacionalidad values(null,314,"COSTA RICA");
insert into nacionalidad values(null,315,"CUBA");
insert into nacionalidad values(null,316,"DOMINICA");
insert into nacionalidad values(null,317,"EL SALVADOR");
insert into nacionalidad values(null,318,"GRANADA");
insert into nacionalidad values(null,319,"GUATEMALA");
insert into nacionalidad values(null,320,"HAITI");
insert into nacionalidad values(null,321,"HONDURAS");
insert into nacionalidad values(null,322,"JAMAICA");
insert into nacionalidad values(null,323,"NICARAGUA");
insert into nacionalidad values(null,324,"PANAMA");
insert into nacionalidad values(null,325,"SAN VICENTE Y LAS GRANADINAS");
insert into nacionalidad values(null,326,"REPUBLICA DOMINICANA");
insert into nacionalidad values(null,327,"TRINIDAD Y TOBAGO");
insert into nacionalidad values(null,328,"SANTA LUCIA");
insert into nacionalidad values(null,329,"SAN CRISTOBAL Y NIEVES");
insert into nacionalidad values(null,380,"ISLAS CAIMÁN");
insert into nacionalidad values(null,381,"ISLAS TURCAS Y CAICOS");
insert into nacionalidad values(null,382,"ISLAS VÍRGENES DE LOS ESTADOS UNIDOS");
insert into nacionalidad values(null,383,"GUADALUPE");
insert into nacionalidad values(null,384,"ANTILLAS HOLANDESAS");
insert into nacionalidad values(null,385,"SAN MARTIN (null,PARTE FRANCESA");
insert into nacionalidad values(null,386,"ARUBA");
insert into nacionalidad values(null,387,"MONTSERRAT");
insert into nacionalidad values(null,388,"ANGUILLA");
insert into nacionalidad values(null,389,"SAN BARTOLOME");
insert into nacionalidad values(null,390,"MARTINICA");
insert into nacionalidad values(null,391,"PUERTO RICO");
insert into nacionalidad values(null,392,"BERMUDAS");
insert into nacionalidad values(null,393,"ISLAS VIRGENES BRITANICAS");
insert into nacionalidad values(null,398,"OTROS PAISES O TERRITORIOS DEL CARIBE Y AMERICA CENTRAL");
insert into nacionalidad values(null,340,"ARGENTINA");
insert into nacionalidad values(null,341,"BOLIVIA");
insert into nacionalidad values(null,342,"BRASIL");
insert into nacionalidad values(null,343,"COLOMBIA");
insert into nacionalidad values(null,344,"CHILE");
insert into nacionalidad values(null,345,"ECUADOR");
insert into nacionalidad values(null,346,"GUYANA");
insert into nacionalidad values(null,347,"PARAGUAY");
insert into nacionalidad values(null,348,"PERU");
insert into nacionalidad values(null,349,"SURINAM");
insert into nacionalidad values(null,350,"URUGUAY");
insert into nacionalidad values(null,351,"VENEZUELA");
insert into nacionalidad values(null,394,"GUAYANA FRANCESA");
insert into nacionalidad values(null,395,"ISLAS MALVINAS");
insert into nacionalidad values(null,399,"OTROS PAISES O TERRITORIOS  DE SUDAMERICA");
insert into nacionalidad values(null,401,"AFGANISTAN");
insert into nacionalidad values(null,402,"ARABIA SAUDI");
insert into nacionalidad values(null,403,"BAHREIN");
insert into nacionalidad values(null,404,"BANGLADESH");
insert into nacionalidad values(null,405,"MYANMAR");
insert into nacionalidad values(null,407,"CHINA");
insert into nacionalidad values(null,408,"EMIRATOS ARABES UNIDOS");
insert into nacionalidad values(null,409,"FILIPINAS");
insert into nacionalidad values(null,410,"INDIA");
insert into nacionalidad values(null,411,"INDONESIA");
insert into nacionalidad values(null,412,"IRAQ");
insert into nacionalidad values(null,413,"IRAN");
insert into nacionalidad values(null,414,"ISRAEL");
insert into nacionalidad values(null,415,"JAPON");
insert into nacionalidad values(null,416,"JORDANIA");
insert into nacionalidad values(null,417,"CAMBOYA");
insert into nacionalidad values(null,418,"KUWAIT");
insert into nacionalidad values(null,419,"LAOS");
insert into nacionalidad values(null,420,"LIBANO");
insert into nacionalidad values(null,421,"MALASIA");
insert into nacionalidad values(null,422,"MALDIVAS");
insert into nacionalidad values(null,423,"MONGOLIA");
insert into nacionalidad values(null,424,"NEPAL");
insert into nacionalidad values(null,425,"OMAN");
insert into nacionalidad values(null,426,"PAKISTAN");
insert into nacionalidad values(null,427,"QATAR");
insert into nacionalidad values(null,430,"COREA");
insert into nacionalidad values(null,431,"COREA DEL NORTE ");
insert into nacionalidad values(null,432,"SINGAPUR");
insert into nacionalidad values(null,433,"SIRIA");
insert into nacionalidad values(null,434,"SRI LANKA");
insert into nacionalidad values(null,435,"TAILANDIA");
insert into nacionalidad values(null,437,"VIETNAM");
insert into nacionalidad values(null,439,"BRUNEI");
insert into nacionalidad values(null,440,"ISLAS MARSHALL");
insert into nacionalidad values(null,441,"YEMEN");
insert into nacionalidad values(null,442,"AZERBAIYAN");
insert into nacionalidad values(null,443,"KAZAJSTAN");
insert into nacionalidad values(null,444,"KIRGUISTAN");
insert into nacionalidad values(null,445,"TADYIKISTAN");
insert into nacionalidad values(null,446,"TURKMENISTAN");
insert into nacionalidad values(null,447,"UZBEKISTAN");
insert into nacionalidad values(null,448,"ISLAS MARIANAS DEL NORTE");
insert into nacionalidad values(null,449,"PALESTINA");
insert into nacionalidad values(null,450,"HONG KONG");
insert into nacionalidad values(null,453,"BHUTÁN");
insert into nacionalidad values(null,454,"GUAM");
insert into nacionalidad values(null,455,"MACAO");
insert into nacionalidad values(null,499,"OTROS PAISES O TERRITORIOS DE ASIA");
insert into nacionalidad values(null,501,"AUSTRALIA");
insert into nacionalidad values(null,502,"FIJI");
insert into nacionalidad values(null,504,"NUEVA ZELANDA");
insert into nacionalidad values(null,505,"PAPUA NUEVA GUINEA");
insert into nacionalidad values(null,506,"ISLAS SALOMON");
insert into nacionalidad values(null,507,"SAMOA");
insert into nacionalidad values(null,508,"TONGA");
insert into nacionalidad values(null,509,"VANUATU");
insert into nacionalidad values(null,511,"MICRONESIA");
insert into nacionalidad values(null,512,"TUVALU");
insert into nacionalidad values(null,513,"ISLAS COOK");
insert into nacionalidad values(null,515,"NAURU");
insert into nacionalidad values(null,516,"PALAOS");
insert into nacionalidad values(null,517,"TIMOR ORIENTAL");
insert into nacionalidad values(null,520,"POLINESIA FRANCESA");
insert into nacionalidad values(null,521,"ISLA NORFOLK");
insert into nacionalidad values(null,522,"KIRIBATI");
insert into nacionalidad values(null,523,"NIUE");
insert into nacionalidad values(null,524,"ISLAS PITCAIRN");
insert into nacionalidad values(null,525,"TOKELAU");
insert into nacionalidad values(null,526,"NUEVA CALEDONIA");
insert into nacionalidad values(null,527,"WALLIS Y FORTUNA");
insert into nacionalidad values(null,528,"SAMOA AMERICANA");
insert into nacionalidad values(null,599,"OTROS PAISES O TERRITORIOS DE OCEANIA");

insert into pensiones values("25-2","CAXDAC");
insert into pensiones values("231001","COLFONDOS");
insert into pensiones values("25-14","COLPENSIONES");
insert into pensiones values("25-3","FONPRECON");
insert into pensiones values("230901","OLD MUTUAL");
insert into pensiones values("230904","OLD MUTUAL ALTERNATIVO");
insert into pensiones values("230301","PORVENIR");
insert into pensiones values("230201","PROTECCION");

insert into eps values("EPSIC3","AIC");
insert into eps values("EPS001","ALIANSALUD");
insert into eps values("ESSC62","ASMETSALUD");
insert into eps values("EPS003","CAFESALUD");
insert into eps values("EPSC34","CAPITAL SALUD");
insert into eps values("EPS015","COLPATRIA");
insert into eps values("CCFC24","COMFAMILIAR HUILA");
insert into eps values("EPS009","COMFENALCO ANTIOQUIA");
insert into eps values("EPS012","COMFENALCO VALLE");
insert into eps values("ESSC33","COMPARTA");
insert into eps values("EPS008","COMPENSAR");
insert into eps values("EPSC22","CONVIDA");
insert into eps values("EPS016","COOMEVA");
insert into eps values("ESSC24","COOSALUD");
insert into eps values("EPS023","CRUZ BLANCA");
insert into eps values("ESSC91","ECOOPSOS");
insert into eps values("ESSC02","EMDISALUD");
insert into eps values("ESSC18","EMSSANAR");
insert into eps values("EPS017","FAMISANAR");
insert into eps values("EPS039","GOLDEN CROSS");
insert into eps values("EPS014","HUMANA VIVIR");
insert into eps values("EPS037","LA NUEVA EPS");
insert into eps values("EPSIC5","MALLAMAS");
insert into eps values("EPS044","MEDIMAS");
insert into eps values("ESSC07","MUTUAL SER");
insert into eps values("EPSIC6","PIJAOS SALUD");
insert into eps values("EPS034","SALUD COLOMBIA");
insert into eps values("EPS047","SALUD MIA");
insert into eps values("EPS046","SALUD TOTAL");
insert into eps values("EPS002","SALUDCOOP");
insert into eps values("EPS013","SALUDVIDA");
insert into eps values("EPS033","SANITAS");
insert into eps values("EPS005","SAVIA SALUD");
insert into eps values("EPS040","SOS");

insert into genero values(null,"Masculino","M");
insert into genero values(null,"Femenino","F");

insert into estadocivil values(null,"Soltero");
insert into estadocivil values(null,"Casado");
insert into estadocivil values(null,"Divorciado");
insert into estadocivil values(null,"Viudo");
insert into estadocivil values(null,"Separado");
insert into estadocivil values(null,"Union Libre");

insert into tipoDocumento values(null,"Cedula Ciudadania","CC");
insert into tipoDocumento values(null,"Cedula Extrangeria","CE");
insert into tipoDocumento values(null,"Pasaporte","PP");

DELIMITER //

CREATE PROCEDURE pr_crearUsuario(
    IN numDocumento INT,
    IN primerNombre VARCHAR(30),
    IN segundoNombre VARCHAR(30),
    IN primerApellido VARCHAR(30),
    IN segundoApellido VARCHAR(30),
    IN clave VARBINARY(200),
    IN fechanacimiento DATE,
    IN numHijos TINYINT,
    IN contactoemer VARCHAR(30),
    IN numcontacto INT,
    IN correo VARCHAR(45),
    IN direccion VARCHAR(45),
    IN telefono INT,
    IN nacionalidad INT,
    IN codigoEPS CHAR(6),
    IN genero INT,
    IN tipoDocumento INT,
    IN estadoCivil INT,
    IN pensionescodigo CHAR(6)
)
BEGIN
    INSERT INTO usuarios (
        numDocumento,
        primerNombre,
        segundoNombre,
        primerApellido,
        segundoApellido,
        clave,
        fechaNac,
        numHijos,
        contactoEmergencia,
        numContactoEmergencia,
        correo,
        direccion,
        telefono,
        nacionalidadId,
        epsCodigo,
        generoId,
        tipoDocumentoId,
        estadoCivil,
        pensionesCodigo
        
    ) VALUES (
        numDocumento,
        primerNombre,
        segundoNombre,
        primerApellido,
        segundoApellido,
        aes_encrypt('clave1',clave),
        fechanacimiento,
        numHijos,
        contactoemer,
        numcontacto,
        correo,
        direccion,
        telefono,
        nacionalidad,
        codigoEPS,
        genero,
        tipoDocumento,
        estadoCivil,
        pensionescodigo
    );
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE pr_modifiUsuario(
    IN numDocumento INT,
    IN primerNombre VARCHAR(30),
    IN segundoNombre VARCHAR(30),
    IN primerApellido VARCHAR(30),
    IN segundoApellido VARCHAR(30),
    IN clave VARBINARY(200),
    IN fechanacimiento DATE,
    IN numHijos TINYINT,
    IN contactoemer VARCHAR(30),
    IN numcontacto INT,
    IN correo VARCHAR(45),
    IN direccion VARCHAR(45),
    IN telefono INT,
    IN nacionalidad INT,
    IN codigoEPS CHAR(6),
    IN genero INT,
    IN tipoDocumento INT,
    IN estadoCivil INT,
    IN pensionescodigo CHAR(6)
)
BEGIN
    UPDATE usuarios AS u
        SET u.primerNombre = primerNombre,
        u.segundoNombre = segundoNombre,
        u.primerApellido = primerApellido,
        u.segundoApellido = segundoApellido,
        u.clave = clave,
        u.fechaNac = fechanacimiento,
        u.numHijos = numHijos,
        u.contactoEmergencia = contactoemer,
        u.numContactoEmergencia = numcontacto,
        u.correo = correo,
        u.direccion = direccion,
        u.telefono = telefono,
        u.nacionalidadId = nacionalidad,
        u.epsCodigo = codigoEPS,
        u.generoId = genero,
        u.tipoDocumentoId = tipoDocumento,
        u.estadoCivilId = estadoCivil,
        u.pensionesCodigo = pensionescodigo
    
		WHERE u.numDocumento = numDocumento;
    
    
END //

DELIMITER ;

DELIMITER // 

CREATE PROCEDURE pr_eliminarUsuario(IN numDocumento INT)
BEGIN
    DELETE FROM usuarios WHERE numDocumento = numDocumento;
END //

DELIMITER ;

DELIMITER //

CREATE FUNCTION f_validarUsuario (numDoc INT)
RETURNS BOOLEAN
BEGIN
    DECLARE bandera BOOLEAN DEFAULT FALSE;
    DECLARE cuenta INT;

   
    SELECT COUNT(*) INTO cuenta FROM usuarios WHERE numDocumento = numDoc;

    
    IF cuenta > 0 THEN
        SET bandera = TRUE;
    END IF;

    RETURN bandera;
END //

DELIMITER ;



DELIMITER //

CREATE TRIGGER tr_verificar_usuario 
BEFORE INSERT ON usuarios
FOR EACH ROW
BEGIN
    DECLARE usuario_existe BOOLEAN;

    SET usuario_existe = f_validarUsuario(NEW.numDocumento);

    IF usuario_existe > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Ya existe un usuario con este número de documento';
        
        ELSE 
        INSERT INTO trazabilidad values(null,NOW(),'%s',correo, NEW.correo, clave,NEW.clave,NEW.numDocumento);
    END IF;
    
END //

DELIMITER ;