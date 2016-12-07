# nette-security
===========================================

This separate component is a part of Single Sign On project.
It contains all security logic. This approach allows simplifying and centralizing any changes related to the security model for the portals that use our SSO.

#### Installation
You can instal this component via composer.
```
"require": {
	"iamvar/nette-security": "*"
}
```

#### Configuration

Let's look an example for Nette

1. register services in Neon configuration

```
parameters:
	sso:
		# here is the fully qualified route for the actions
		consumeAction: 'Frontend:AssertionConsumer:consume'
		defaultAction: 'Frontend:Homepage:default'
		# IdP login and logout urls
		loginUrl: 'https://login.sso.com/sign/acs'
		logoutUrl: 'https://login.sso.com/sign/out'

services:
	identitySerializer: Iamvar\NetteSecurity\IdentitySerializer
	identityResolver: Iamvar\NetteSecurity\IdentityResolver
```

2. extend your base presenter from
```
Iamvar\NetteSecurity\presenters\SecuredBasePresenter
```
or
```
Iamvar\NetteSecurity\presenters\UnSecuredBasePresenter
```

3. create AssertionConsumerPresenter that extends
```
Iamvar\NetteSecurity\presenters\AssertionConsumerBasePresenter
```

4. update your actionOut in SignPresenter
```
public function actionOut()
	{
		parent::actionOut();
	}
```

It is also possible to configure allow and deny users or groups
configuration example
```
parameters:
	accessRight:
		insecureRoutes:
			- :Error
		allow:
			users:
				vasya@email.local
			groups:
				- admin
		deny:
			users:
				- vasily@email.local
			groups:
				- notadmin

services:
	authorizator: Iamvar\NetteSecurity\authorization\Authorizator(%accessRight%)
```

#### Developers environment
You may easily mock service using configuration
```
	parameters:
		ssoMock:
			validCredentials:
				username1:
					password: 'test1'
					parameters:
						data:
							userinfo:
								lastName: 'Smith'
								firstName: 'Johnn'
								department: 'Roll-out manager'
				username2:
					password: 'test2'
					parameters:
						data:
							userinfo:
								lastName: 'Wood'
								firstName: 'Bob'
								department: 'Director'

	services:
		identityGenerator:
			class: Iamvar\NetteSecurity\Mocks\IdentityGenerator(%ssoMock%)

		identityResolver:
			class: Iamvar\NetteSecurity\Mocks\IdentityResolverMock(@identityGenerator)
```