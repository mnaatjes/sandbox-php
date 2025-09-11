# Application Lifecycle

1. Create **Application Instance** in `~/bootstrap/app.php`
   1. Assign `dirname(__DIR__)` to single parameter
2. **Application Constructor**
   1. Validates *Instance Count*
   2. Increments Instance Count
   3. **Configure Application**
      1. Create **Path** instance of `dirname(__DIR__)`
      2. Validate Path
      3. Load Environment Variables
      4. Assign Configuration Paths
3. **Parent Constructor**
   1. Instantiate *ReflectionCache*