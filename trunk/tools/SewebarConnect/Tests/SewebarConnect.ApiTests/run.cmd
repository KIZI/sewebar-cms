@ECHO OFF

SET CURRENT_DIR=%CD%
CD %~dp0

SET node_modules=node_modules

IF NOT EXIST "%node_modules%" (
	CALL npm install
	IF ERRORLEVEL 1 GOTO FAIL
)

IF NOT EXIST "%node_modules%/SewebarConnect" (
	CALL npm install ../../Sources/SewebarConnectClient/
	IF ERRORLEVEL 1 GOTO FAIL
)

ECHO Building SewebarConnect.Tests.sln ...
"msbuild.exe" "..\SewebarConnect.Tests.sln" /t:Rebuild /p:Configuration=Release > %root%..\..\build_tests_log.txt
IF ERRORLEVEL 1 GOTO FAIL
GOTO RUN

:RUN
CALL %node_modules%\.bin\mocha.cmd
IF ERRORLEVEL 1 GOTO FAIL
GOTO END

:FAIL
CD %CURRENT_DIR%
PAUSE
Exit /B 1

:END
CD %CURRENT_DIR%
Exit /B 0