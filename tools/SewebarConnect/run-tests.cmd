@ECHO OFF

SET CURRENT_DIR=%CD%

CD Tests\SewebarConnect.ApiTests

SET node_modules=node_modules

IF NOT EXIST "%node_modules%" GOTO INSTALL

GOTO RUN

:INSTALL
call npm install
call npm install ../../Sources/SewebarConnectClient/
"msbuild.exe" "..\SewebarConnect.Tests.sln" /t:Rebuild /p:Configuration=Release > ..\..\build_tests_log.txt
IF ERRORLEVEL 1 GOTO FAIL
GOTO RUN

:RUN
call %node_modules%\.bin\mocha.cmd
IF ERRORLEVEL 1 GOTO FAIL
GOTO END

:FAIL
CD %CURRENT_DIR%
PAUSE
Exit /B 1

:END
CD %CURRENT_DIR%
Exit /B 0