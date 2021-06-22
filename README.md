# Service Container example

This is an example of a _Service Container_ PHP implementation providing the following features:

- Bind a concrete implementation to an abstract implementation
  
```
$container->bind(AbstractClass::class, ConcreteClass::class);
var_dump($container->resolve(AbstractClass::class)); // ConcreteClass::class 
```
- Bind a singleton object to an abstract implementation

```
$container->singleton(AbstractClass::class, ConcreteClass::class);
$implementation1 = $container->resolve(AbstractClass::class);
$implementation2 = $container->resolve(AbstractClass::class);
echo ($implementation1 === $implementation2); // true
```

- Bind a callable (closure or invokable class) to an abstract implementation
  
```
$container->bind(AbstractClass::class, function ($container) {
  return 'hi there!';
});
var_dump($container->resolve(AbstractClass::class)); // 'hi there!'
```
- Recursive dependency resolution

```
class AClassWithDependencies
{
    public function __construct(public AClass $dependency)
    {

    }
}

class AClass
{

}

...

$container->bind(AbstractClass::class, AClassWithDependencies::class);
$instance = $container->resolve(AbstractClass::class);
// make an instance of AClassWithDependencies, built by recursively building its constructor 
// dependencies (i.e. AClass)
```