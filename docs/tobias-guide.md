There are some peculiarities in the communication with Tobias AX. Some of those are documented here.

# Order of fields

The order of the fields important and must be exactly as specified. This is usually in alphabetical order, but this might not always be the case.

When fields are out of order there are no error messages, but certain fields will be ignored.

# Nationality

Nationality is special property that has its own set of rules. It is a property of the Person entity, but when creating a person it does not get saved unless you also specify it as a property of the PropertySeeker entity.

However when updating the Person the nationality should only be specified for the Person entity and not on the PropertySeeker entity. Otherwise you will get an error about the person not being 18 years of age
