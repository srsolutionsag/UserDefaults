# ToDo

## Refactorings
### Target Folder Structure
```
classes/
    class.ilUserDefaultsConfigGUI.php
    class.ilUserDefaultsPlugin.php
    class.ilUserDefaultsRestApiGUI.php
    class.AssignmentProcessesGUI.php
    class.AssignmentProcessRulesGUI.php
    class.ApplyAssignmentProcessesManuallyGUI.php
src/
    UserDefaultsApi.php
    Adapters/
        Api
        Config
        Persistence
        UiComponents
    Domain/
        Model/
        Ports/
        



Remove the folders and refactore the classes to the hexagonal port and adapter structure
src/Access
src/Form
src/UDFCheck
src/UserSetting
src/Utils
src/UserSearch



```
### withPHP 8.1

implement readonly properties

```
    private function __construct(
        public readonly int $id, public readonly string $title
    )
```