<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Provider;

use Fp\BadaBoomBundle\ChainNode\Provider\SecurityContextProvider;
use BadaBoom\Context;

class SecurityContextProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('Fp\BadaBoomBundle\ChainNode\Provider\SecurityContextProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithSecurityContextAsArgument()
    {
        new SecurityContextProvider($this->createTokenStorageMock());
    }

    /**
     * @test
     */
    public function shouldDoNothingIfContextNotHaveToken()
    {
        $chain = new SecurityContextProvider($this->createTokenStorageMock());

        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->never())
            ->method('setVar')
        ;

        $chain->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $context = new Context(new \Exception);

        $chain = new SecurityContextProvider($this->createTokenStorageMock());

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $chain->nextNode($nextChainNodeMock);

        $chain->handle($context);
    }

    /**
     * @test
     */
    public function shouldAddUserInformationIfContextHaveTokenWithUserAsString()
    {
        $expectedDefaultSection = 'security';
        $expectedUser = 'the user';
        $expectedUserData = array(
            'user' => $expectedUser
        );

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedDefaultSection),
                $this->equalTo($expectedUserData)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock);

        $chain->handle($contextMock);
    }

    /**
    * @test
    */
    public function shouldAddUserInformationToCustomSectionIfContextHaveTokenWithUserAsString()
    {
        $expectedCustomSection = 'custom_security_section';
        $expectedUser = 'the user';

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedCustomSection)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock, $expectedCustomSection);

        $chain->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldAddUserInformationIfContextHaveTokenWithUserInterface()
    {
        $expectedDefaultSection = 'security';

        $expectedUsername = 'the user object username';
        $expectedUser = $this->createUserMock();
        $expectedUser
            ->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue($expectedUsername))
        ;

        $expectedUserData = array(
            'user' => $expectedUsername
        );

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedDefaultSection),
                $this->equalTo($expectedUserData)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock);

        $chain->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldAddUserInformationToCustomSectionIfContextHaveTokenWithUserInterface()
    {
        $expectedCustomSection = 'custom_security_section';
        $expectedUser = $this->createUserMock();

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedCustomSection)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock, $expectedCustomSection);

        $chain->handle($contextMock);
    }

    protected function createTokenStorageMock()
    {
        return $this->createMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
    }

    protected function createTokenMock()
    {
        return $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }

    protected function createChainNodeMock()
    {
        return $this->createMock('BadaBoom\ChainNode\ChainNodeInterface');
    }

    protected function createUserMock()
    {
        return $this->createMock('Symfony\Component\Security\Core\User\UserInterface');
    }

    protected function createContextMock()
    {
        return $this
            ->getMockBuilder('BadaBoom\Context')
            ->setConstructorArgs(array(new \Exception))
            ->getMock();
    }
}