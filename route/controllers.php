<?php
/**
 * For requests directly to controllers
 */
$ROUTE->get("/verify/[vLink]", function () {process("VerificationController::verifyUser");});