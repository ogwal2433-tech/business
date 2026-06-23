!macro customInit
  !define UNINSTALL_REG_PATH "Software\Microsoft\Windows\CurrentVersion\Uninstall\{${APP_GUID}}_is1"

  ReadRegStr $R0 HKLM "${UNINSTALL_REG_PATH}" "UninstallString"
  StrCmp $R0 "" check_hkcu found

check_hkcu:
  ReadRegStr $R0 HKCU "${UNINSTALL_REG_PATH}" "UninstallString"
  StrCmp $R0 "" done found

found:
  ReadRegStr $R1 HKLM "${UNINSTALL_REG_PATH}" "DisplayName"
  StrCmp $R1 "" get_hkcu_name got_name

get_hkcu_name:
  ReadRegStr $R1 HKCU "${UNINSTALL_REG_PATH}" "DisplayName"
  StrCmp $R1 "" default_name got_name

default_name:
  StrCpy $R1 "${PRODUCT_NAME}"

got_name:
  ReadRegStr $R2 HKLM "${UNINSTALL_REG_PATH}" "InstallLocation"
  StrCmp $R2 "" use_hkcu_dir have_dir

use_hkcu_dir:
  ReadRegStr $R2 HKCU "${UNINSTALL_REG_PATH}" "InstallLocation"

have_dir:
  MessageBox MB_YESNO|MB_ICONEXCLAMATION|MB_DEFBUTTON2 "${PRODUCT_NAME} is already installed.$\r$\n$\r$\nWould you like to uninstall the previous version first?" /SD IDNO IDYES uninstall_prev IDNO abort_install

uninstall_prev:
  StrCpy $R0 "$R2\Uninstall ${PRODUCT_NAME}.exe"
  IfFileExists $R0 run_uninstall
  StrCpy $R0 "$R2\uninstall.exe"
  IfFileExists $R0 run_uninstall
  goto done

run_uninstall:
  ExecWait '"$R0" /S _?=$R2'
  goto done

abort_install:
  Abort

done:
!macroend
