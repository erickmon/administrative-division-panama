# Hola!

Preparé este espacio para mostrar un poco mi trabajo, en este caso relacionado con un proyecto que trabajé en su momento relacionado con la división administrativa de Panamá hasta [lugar poblado/asentamientos](https://www.inec.gob.pa/buscador/Default.aspx?BUSCAR=lugares%20poblados).

## Requerimiento

Se necesitaba compartir con diferentes áreas del negocio información relacionada con los lugares poblados, el problema es que esta información solo la tenían en un archivo Excel (*.xls), básicamente era convertir el Excel a una aplicación con las funcionalidades que les fuera de utilidad.

Era importante que los datos se pudieran migrar en lo posible a la aplicación, era más sencillo migrar los datos y corregir en la aplicación que cargar todo nuevamente.

## Solución

Uno de los problemas de usar Excel como fuente principal de información es que se presta para errores humanos por lo que era muy común encontrar inconsistencia de datos, así que se propuso migrar solamente los datos que en su mayoría fuera consistente y se había algún error se pudiera corregir desde la aplicación.

Después de migrar, implementar la aplicación y corregir los datos que fueron migrados con errores quedó información que podía ser utilizada en entorno productivo y por otras aplicaciones, por ejemplo, una de ellas era un sistema que se encargaba de gestionar las ventas (también lo desarrollé, pronto estaré compartiendo más información) que necesitaba validar la dirección el cliente antes de proceder con una venta.

## El código
[Este proyecto](https://github.com/erickmon/administrative-division-panama) simula la solución que preparé en su momento para el negocio siguiendo distintas etapas.

Fue desarrollado con [Laravel 8](https://laravel.com/docs/8.x), siguiendo se utilizó Docker para preparar el entorno local. 

El objetivo es tener una nueva fuente de datos de direcciones hasta lugar poblado que pueda servir para futuros proyectos personales o cualquier persona que le sea de utilidad, estas son las etapas contempladas (hasta este momento):

### Migrar el listado de [lugares poblados](https://www.inec.gob.pa/archivos/P3551Nomenclatura.xls) del [Instituto Nacional de Estadística y Censo](https://www.inec.gob.pa/) a una tabla en una base de datos relacional (MySQL).
Se utilizará "Nomenclatura o listado alfabético de los lugares de la República" como fuente de datos, hasta este momento la fuente es del 2010 por lo que será necesario depurar la data, al menos hasta corregimiento.

### Procesar los datos de INEC y pasarlos en una estructura de tablas relacionadas
Para hacer esto se optó por utilizar la concatenación del nombre de cada elemento en la estructura organizacional (provincia, distrito, corregimiento, asentamiento) como llave como identificador, por ejemplo:

- Provincia key:       Nombre de la provincia
- Distrito key:       Nombre de la provincia + Nombre del distrito
- Corregimiento key:   Nombre de la provincia + Nombre del distrito + Nombre del corregimiento
- Asentamiento key:    Nombre de la provincia + Nombre del distrito + Nombre del corregimiento + Nombre del asentamiento

De allí se le indica al sistema las instrucciones necesarias para cargar los datos en las tablas correspondientes, la lógica utilizada puede verse dentro de [app/Console/Commands/CreateRecordsFromINEC.php](https://github.com/erickmon/administrative-division-panama/blob/main/app/Console/Commands/CreateRecordsFromINEC.php)

### Depuración de Datos
Los datos oficiales publicados por el Gobierno de Panamá (INEC) tenían errores ortográficos, estaban desactualizados, etc. por lo que era necesario realizar un trabajo de depuración y actualización.

La mayor parte de la depuración se realizó directamente en la base de datos ya que me pareció la solución más rápida.

La actualización de datos se realizó tomando en cuenta los cambios en la organización territorial según las Gacetas Oficiales:

Bocas del Toro
- [https://www.gacetaoficial.gob.pa/pdfTemp/29169_B/GacetaNo_29169b_20201204.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/29169_B/GacetaNo_29169b_20201204.pdf)
- [http://gacetas.procuraduria-admon.gob.pa/27801-A_2015.pdf](http://gacetas.procuraduria-admon.gob.pa/27801-A_2015.pdf)
- [https://www.gacetaoficial.gob.pa/pdfTemp/27801_A/51095.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/27801_A/51095.pdf)

Chiriquí
- [http://gacetas.procuraduria-admon.gob.pa/27374_2013.pdf](http://gacetas.procuraduria-admon.gob.pa/27374_2013.pdf)
- [http://gacetas.procuraduria-admon.gob.pa/28465-A_2018.pdf](http://gacetas.procuraduria-admon.gob.pa/28465-A_2018.pdf)

Coclé
- [https://www.gacetaoficial.gob.pa/pdfTemp/28276_B/GacetaNo_28276b_20170511.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/28276_B/GacetaNo_28276b_20170511.pdf)
- [http://gacetas.procuraduria-admon.gob.pa/27377_2013.pdf](http://gacetas.procuraduria-admon.gob.pa/27377_2013.pdf)
- [https://www.gacetaoficial.gob.pa/pdfTemp/26774/GacetaNo_26774_20110428.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/26774/GacetaNo_26774_20110428.pdf)

Colón
- [http://gacetas.procuraduria-admon.gob.pa/27634-A_2014.pdf](http://gacetas.procuraduria-admon.gob.pa/27634-A_2014.pdf)
- [https://www.gacetaoficial.gob.pa/pdfTemp/28469_B/66175.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/28469_B/66175.pdf)

Darién
- [https://www.gacetaoficial.gob.pa/pdfTemp/28322_A/62762.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/28322_A/62762.pdf)

Herrera
- [https://www.gacetaoficial.gob.pa/pdfTemp/27032_A/GacetaNo_27032a_20120511.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/27032_A/GacetaNo_27032a_20120511.pdf)

Los Santos
- [https://www.gacetaoficial.gob.pa/pdfTemp/27415/GacetaNo_27415_20131115.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/27415/GacetaNo_27415_20131115.pdf)

Panamá
- [https://www.gacetaoficial.gob.pa/pdfTemp/27032_A/GacetaNo_27032a_20120511.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/27032_A/GacetaNo_27032a_20120511.pdf)

Panamá Oeste
- [https://www.gacetaoficial.gob.pa/pdfTemp/27443_A/44900.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/27443_A/44900.pdf)

Veraguas
- [https://www.gacetaoficial.gob.pa/pdfTemp/27032_B/GacetaNo_27032b_20120511.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/27032_B/GacetaNo_27032b_20120511.pdf)
- [http://gacetas.procuraduria-admon.gob.pa/27032-A_2012.pdf](http://gacetas.procuraduria-admon.gob.pa/27032-A_2012.pdf)
- [http://gacetas.procuraduria-admon.gob.pa/27644-A_2014.pdf](http://gacetas.procuraduria-admon.gob.pa/27644-A_2014.pdf)
- [https://www.gacetaoficial.gob.pa/pdfTemp/28397_C/GacetaNo_28397c_20171030.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/28397_C/GacetaNo_28397c_20171030.pdf)
- [https://www.gacetaoficial.gob.pa/pdfTemp/28462_B/GacetaNo_28462b_20180208.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/28462_B/GacetaNo_28462b_20180208.pdf)

Comarca Naso Tjër Di
- [https://www.gacetaoficial.gob.pa/pdfTemp/29170_A/GacetaNo_29170a_20201207.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/29170_A/GacetaNo_29170a_20201207.pdf)

Comarca Ngäbe-Buglé
- [https://www.gacetaoficial.gob.pa/pdfTemp/27032_B/GacetaNo_27032b_20120511.pdf](https://www.gacetaoficial.gob.pa/pdfTemp/27032_B/GacetaNo_27032b_20120511.pdf)
- [http://gacetas.procuraduria-admon.gob.pa/27032-A_2012.pdf](http://gacetas.procuraduria-admon.gob.pa/27032-A_2012.pdf)

### Crear una fuente de datos a partir de las tablas relacionadas
Después de cargar los datos los he exportado a JSON y CSV, están disponible en:

- [csv](https://github.com/erickmon/administrative-division-panama/blob/gh-pages/csv/)
- [json](https://github.com/erickmon/administrative-division-panama/blob/gh-pages/json/)

### Consulta de datos (En Desarrollo)
Con [Angular (CLI: 11.0.5)](https://angular.io/) desarrollé una aplicación para consultar los datos creados, está disponible en:

- [https://github.com/erickmon/near](https://github.com/erickmon/near)

Parte del código fue desarrollado tomando elementos del tutorial [Tour of Heroes](https://angular.io/tutorial) por lo que he dejado la referencia de los derechos de autor de Google en los mismos, por ejemplo:

- [https://github.com/erickmon/near/blob/main/src/app/messages/messages.component.ts](https://github.com/erickmon/near/blob/main/src/app/messages/messages.component.ts)

En este momento estoy desarrollando el servicio que se encargará de la lógica de consultar los datos y devolver los resultados en JSON, por lo pronto al escribir el nombre de la dirección devuelve todas las provincias que se encuentran en:

- [https://raw.githubusercontent.com/erickmon/administrative-division-panama/gh-pages/json/provinces.json](https://raw.githubusercontent.com/erickmon/administrative-division-panama/gh-pages/json/provinces.json)
