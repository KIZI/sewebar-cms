@echo off

PUSHD.
CD ".\Sources\SewebarConnect"
SubWCRev . SVNDataAttribute.template.cs SVNDataAttribute.cs -mf
if errorlevel 1 goto error
POPD

ECHO.

rem assuming msbuild is in system path, ie. "C:\Windows\Microsoft.NET\Framework\v4.0"

echo Build started...
"msbuild.exe" ".\Sources\SEWEBAR Connect.sln" /t:Rebuild /p:Configuration=Release > build_log.txt
if errorlevel 1 goto error

goto end

:error
echo Failed
pause
exit /B 1

:end
echo Success
exit /B 0