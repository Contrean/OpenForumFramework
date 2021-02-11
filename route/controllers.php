<?php
/**
 * For requests directly to controllers
 */
$ROUTE->get("/verify/[vLink]", function () {process("VerificationController::verifyUser");});
$ROUTE->get("/reset/[vLink]", function () {process("VerificationController::resetPassword");});
$ROUTE->get("/resetPw/[userId]", function () {process("UserController::resetPassword");});
$ROUTE->post("/createUser", function () {process("UserController::createUser");});