# Prompts and Queryies

## 1.0 Value Objects:

How do you enforce immutability of a RegistryItem object once it is registered?

A: Any modification should result in a new RegistryItem Instance

What happens if a value is changed? e.g. $item = lookup(some.value.here) $item++ register($item)

## 2.0 Framework and "Smart" Methods

In the context of a framework using a single Registry: 

When is it appropriate to assign smart methods, e.g. for a filesystem part of the registry and registerPath() method?

How do you enforce a convention like this - e.g. [#Attribute...] declarations before certain framework classes; special methods the Registry looks for; a constant that defines these parameters?

## 3.0 Descriptive vs Instructive Metadata

Why is one implemented over the other?

What are the advantages?

If you employ a meta-data object, is it assumed that type-casting will be used?

What are real, applied examples of each?

What is the goal of each? What are the benefits?

When does each make sense?

## 4.0 Hash Maps

Is a registry a tree of nested associative arrays - e.g. ["some"=>["thing"=>["here"=>Item()]]]?

Is a hash-map by definition a tree nested associative array or can it be something different?

Is a hash-map a linked list or something different? 

What is a hash-map? Does it have nodes? Aside from key => value, what properties does each element have - e.g. next?

What is a hash-table and how is it differnet from a hash-map?

What is a hash-set and how is it different from a hash-map?

## 5.0 Keys and aliases

What are the strategies for assigning, enforcing top-level keys?

How would you apportion top-level keys within a framework of classes and some associated helper functions?

Should the initialized registry establish sub-arrays for the top-level keys?

How is schema-validation implemented? What are the best practices?

How would you apply specialized registry methods within classes accross a framework in a unified pattern?

If $driver is the top-level key, $key can still be in dot-notation for nesting within the associatve array? e.g. drivername.keypart.anotherkeypart.item.

The example class diagram shows the special methods for registering within the service registry. This seems weird and doesn't allow for other top-level keys to be created. What is a pattern one can enforce to allow individual classes to register with their own prefix's?



## 6.0 Searching 

"Allows searching entire branches of the heirarchy"



