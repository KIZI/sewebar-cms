@ECHO OFF

SET root=%~dp0

IF EXIST "%root%node_modules/SewebarConnect" (
	ECHO Removing "%root%node_modules/SewebarConnect"
	rd /S /Q "%root%node_modules/SewebarConnect"
)

ECHO.

IF EXIST "%root%output" (
	ECHO Removing "%root%output"
	rd /S /Q "%root%output"
)