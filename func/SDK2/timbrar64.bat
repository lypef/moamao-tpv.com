@echo off
%~dp0php -c %~dp0php.ini -f %~dp0sdk.so %1
ECHO ************************************
ECHO *****    PROCESO FINALIZADO    *****
ECHO ************************************