<?php

namespace Fp\BadaBoomBundle\Tests\ChainNode\Provider;

use BadaBoom\ChainNode\ChainNodeInterface;
use BadaBoom\ChainNode\Provider\AbstractProvider;
use Fp\BadaBoomBundle\ChainNode\Provider\SecurityContextProvider;
use BadaBoom\Context;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityContextProviderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass(SecurityContextProvider::class);

        $this->assertTrue($rc->isSubclassOf(AbstractProvider::class));
    }

    /**
     * @test
     */
    public function shouldDoNothingIfContextNotHaveToken()
    {
        $chain = new SecurityContextProvider($this->createTokenStorageMock());

        $contextMock = $this->createContextMock();
        $contextMock->expects($this->never())
            ->method('setVar');

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
        $nextChainNodeMock->expects($this->once())
            ->method('handle')
            ->with($context);

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
        $tokenMock->expects($this->once())
            ->method('getUser')
            ->willReturn($expectedUser);

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);

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
        $tokenMock->expects($this->once())
            ->method('getUser')
            ->willReturn($expectedUser);

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);

        $contextMock = $this->createContextMock();
        $contextMock->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedCustomSection)
            );

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
        $expectedUser->expects($this->once())
            ->method('getUsername')
            ->willReturn($expectedUsername);

        $expectedUserData = array(
            'user' => $expectedUsername
        );

        $tokenMock = $this->createTokenMock();
        $tokenMock->expects($this->once())
            ->method('getUser')
            ->willReturn($expectedUser);

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);

        $contextMock = $this->createContextMock();
        $contextMock->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedDefaultSection),
                $this->equalTo($expectedUserData)
            );

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
        $tokenMock->expects($this->once())
            ->method('getUser')
            ->willReturn($expectedUser);

        $securityContextMock = $this->createTokenStorageMock();
        $securityContextMock->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);

        $contextMock = $this->createContextMock();
        $contextMock->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedCustomSection)
            );

        $chain = new SecurityContextProvider($securityContextMock, $expectedCustomSection);

        $chain->handle($contextMock);
    }

    protected function createTokenStorageMock()
    {
        return $this->createMock(TokenStorageInterface::class);
    }

    protected function createTokenMock()
    {
        return $this->createMock(TokenInterface::class);
    }

    protected function createChainNodeMock()
    {
        return $this->createMock(ChainNodeInterface::class);
    }

    protected function createUserMock()
    {
        return $this->createMock(UserInterface::class);
    }

    protected function createContextMock()
    {
        return $this->getMockBuilder(Context::class)
            ->setConstructorArgs(array(new \Exception))
            ->getMock();
    }
}