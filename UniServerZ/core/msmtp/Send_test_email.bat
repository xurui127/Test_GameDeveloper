@ECHO OFF
rem #############################################################
rem # Name: Send_test_email.bat
rem # Created By: The Uniform Server Development Team
rem # Send email from command line via SMTP using MSMTP
rem # The ECHO. line after ECHO Subject: is important and must be included
rem ##############################################################


rem ### working directory current folder 
pushd %~dp0
set HOME=..\..\

ECHO Subject: This is a test   >> %TEMP%\temp.mail
ECHO.                          >> %TEMP%\temp.mail
ECHO Testing (your content).   >> %TEMP%\temp.mail
ECHO More content blah blah..  >> %TEMP%\temp.mail
ECHO.                          >> %TEMP%\temp.mail
ECHO blah.                     >> %TEMP%\temp.mail

msmtp --file=%cd%\msmtprc.ini user_name@target_email_address -t < %TEMP%\temp.mail

DEL %TEMP%\temp.mail

pause

rem ### restore original working directory
popd
