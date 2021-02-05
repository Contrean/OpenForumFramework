<?php
/**
 * For requests directly to controllers
 */
$ROUTE->get("/verify/[vLink]", function () {process("VerificationController::verifyUser");});
$ROUTE->get("/reset/[vLink]", function () {process("VerificationController::resetPassword");});