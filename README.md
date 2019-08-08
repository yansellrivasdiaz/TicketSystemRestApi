Ticket System Rest Api
======================

**WARNING**: This distribution does not support Symfony 4.

***Desarrollado por: Yansell Rivas Diaz***

Requerimientos para la instalación:
=================

  * Instalación de un servidor con PHP.
  * Instalación de composer en tu ordenador.
  * Instalación de GIT (Solo si va a clonar el repositorio)
  [link aqui...](https://github.com/yansellrivasdiaz/TicketSystemRestApi). 
---------------------------------------------------------------
Instalación de la Aplicación:
============================
  
   * Ir mediante la terminar de linux o CMD windows a donde 
   quieras instalar la aplicación web.
   * Clonar el [Repositorio](https://github.com/yansellrivasdiaz/TicketSystemRestApi) desde github o descargar
   a tu ordenador y descomprimir  en la carpeta donde deseas acceder 
   en modo local.
   * Una vez descargado o clonado debes ejecutar los siguientes comandos:
     
     * **Composer install**
     * **Editar archivo ***app/config/parameters.yml*** 
     y cambiar los parámetros de conexión a la base de datos (***Nota: solo si no agregaste los parametros al momento de ejecutar composer install***)**
     * **php bin/console doctrine:database:create**
     * **php bin/console doctrine:schema:update --force**
     * **php bin/console doctrine:fixtures:load** ***(Nota: una vez ejecute el comando saldrá: Careful, database will bu purged. Do you want to continue? (yes/no) [no]: yes*** y luego darle a enter).
     * **php bin/console cache:clear --env=prod** (Para limpiar la cache del modo produccion)
     
   * Ahora puedes acceder a la ruta del proyecto ruta:
    127.0.0.1(o Nombre del servidor)/ticket_system.
    
**Nota:** Los datos de accesos al sistema son los siguientes:
    
    * email: admin@admin.com 
    * Password: admin    

Rutas de accesos:
============
  * **Login:**
    * ruta: ***http://127.0.0.1:8000/api/login***
    * method: ***POST*** 
    * parametros: ***[email,password]*** 
  * **Authenticated Token Info:**
    * ruta: ***http://127.0.0.1:8000/api/verifytoken***
    * method: ***GET*** 
----------------------------------------------------
  * **employees get:**
    * ruta: ***http://127.0.0.1:8000/api/employees***
    * method: ***GET*** 
  * **employees store:**
    * ruta: ***http://127.0.0.1:8000/api/employee***
    * method: ***POST*** 
    * parametros: ***[firstname,lastname,email,password]***
  * **employee get:**
    * ruta: ***http://127.0.0.1:8000/api/employee/id***
    * method: ***GET*** 
  * **employee lock:**
    * ruta: ***http://127.0.0.1:8000/api/employee/id/lock***
    * method: ***PUT*** 
  * **employee unlock:**
    * ruta: ***http://127.0.0.1:8000/api/employee/id/unlock***
    * method: ***PUT*** 
----------------------------------------------------
  * **tickets get:**
    * ruta: ***http://127.0.0.1:8000/api/tickets***
    * method: ***GET*** 
  * **ticket store:**
    * ruta: ***http://127.0.0.1:8000/api/ticket***
    * method: ***POST*** 
    * parametros: ***[subject,description,userId,employees,status]***
  * **ticket get:**
    * ruta: ***http://127.0.0.1:8000/api/ticket/id***
    * method: ***GET*** 
  * **ticket update:**
    * ruta: ***http://127.0.0.1:8000/api/ticket/id***
    * method: ***PUT*** 
    * parametros: ***[subject,description,userId,employees,status]***
  * **ticket delete:**
    * ruta: ***http://127.0.0.1:8000/api/ticket/id***
    * method: ***DELETE***
  * **ticket close:**
    * ruta: ***http://127.0.0.1:8000/api/ticket/id/close***
    * method: ***PUT*** 
----------------------------------------------------
  * **time entries store:**
    * ruta: ***http://127.0.0.1:8000/api/ticket/id/note***
    * method: ***POST*** 
    * parametros: ***[note,userId,ticketId]***
  * **time entries delete:**
    * ruta: ***http://127.0.0.1:8000/api/note/id***
    * method: ***DELETE*** 
----------------------------------------------------
  * **ticket reports:**
    * ruta: ***http://127.0.0.1:8000/reports/tickets***
    * method: ***POST*** 
    * parametros: ***[startdate,enddate]***
---------------------------------------------------- 
    
***©copyright 2019 Ing. Yansell Rivas***