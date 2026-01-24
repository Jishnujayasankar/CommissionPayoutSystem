@echo off
echo Creating database...

REM Update these paths if your MySQL is installed elsewhere
set MYSQL_PATH=C:\xampp\mysql\bin
set MYSQL_PATH2=C:\Program Files\MySQL\MySQL Server 8.0\bin

REM Try XAMPP path first
if exist "%MYSQL_PATH%\mysql.exe" (
    "%MYSQL_PATH%\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS commissionpayoutsystem;"
    echo Database created successfully!
    goto :done
)

REM Try MySQL Server path
if exist "%MYSQL_PATH2%\mysql.exe" (
    "%MYSQL_PATH2%\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS commissionpayoutsystem;"
    echo Database created successfully!
    goto :done
)

REM Try system PATH
mysql -u root -e "CREATE DATABASE IF NOT EXISTS commissionpayoutsystem;" 2>nul
if %errorlevel% equ 0 (
    echo Database created successfully!
    goto :done
)

echo.
echo ERROR: MySQL not found!
echo Please create the database manually using phpMyAdmin or MySQL Workbench
echo Database name: commissionpayoutsystem
echo.

:done
pause
