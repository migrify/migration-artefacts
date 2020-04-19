<?php

declare(strict_types=1);

namespace Migrify\MigrationArtefact\SpaghettiToSymfonyController\Rector\NodeFactory;

use Migrify\MigrationArtefact\SpaghettiToSymfonyController\Rector\ValueObject\SymfonyClass;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;

final class ControllerClassFactory
{
    /**
     * @var BuilderFactory
     */
    private $builderFactory;

    /**
     * @var InvokeClassMethodFactory
     */
    private $invokeClassMethodFactory;

    /**
     * @var ContentClassMethodFactory
     */
    private $contentClassMethodFactory;

    public function __construct(
        BuilderFactory $builderFactory,
        InvokeClassMethodFactory $invokeClassMethodFactory,
        ContentClassMethodFactory $contentClassMethodFactory
    ) {
        $this->builderFactory = $builderFactory;
        $this->invokeClassMethodFactory = $invokeClassMethodFactory;
        $this->contentClassMethodFactory = $contentClassMethodFactory;
    }

    /**
     * @param Node[] $nodes
     */
    public function createFromNameAndStmts(string $controllerClassName, string $routePath, array $nodes): Class_
    {
        $controllerClassBuilder = $this->builderFactory->class($controllerClassName);
        $controllerClassBuilder->makeFinal();
        $controllerClassBuilder->extend(new FullyQualified(SymfonyClass::ABSTRACT_CONTROLLER_CLASS));

        $classStmts = [];
        $classStmts[] = $this->invokeClassMethodFactory->create($routePath);
        $classStmts[] = $this->contentClassMethodFactory->create($nodes, $routePath);

        $controllerClassBuilder->addStmts($classStmts);

        return $controllerClassBuilder->getNode();
    }
}