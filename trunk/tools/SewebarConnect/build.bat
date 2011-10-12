@echo off

echo Build started...
"msbuild.exe" ".\SEWEBAR Connect.sln" /rebuild "Release" /out build_log.txt
if errorlevel 1 goto error

goto end

:error
echo Failed
pause
exit /B 1

:end
echo Success
exit /B 0