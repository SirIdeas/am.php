<?php

// Se debe iniciar la sesion
session_start();

// Asignar id de la sesion
AmSession::setSessionId(Am::getAttribute("session"));
