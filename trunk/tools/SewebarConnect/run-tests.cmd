@ECHO OFF

rem API Tests
CALL Tests\SewebarConnect.ApiTests\run.cmd
IF ERRORLEVEL 1 GOTO FAIL
GOTO END

:FAIL
Exit /B 1

:END
Exit /B 0