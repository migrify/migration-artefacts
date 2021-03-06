<?php

declare(strict_types=1);

namespace Migrify\MigrationArtefact\SpaghettiToSymfonyController\Rector;

use Migrify\MigrationArtefact\SpaghettiToSymfonyController\DataProvider\OldControllerPathsDataProvider;
use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\RectorDefinition;

final class ReplaceFilePathWithRouteRector extends AbstractRector
{
    /**
     * @var OldControllerPathsDataProvider
     */
    private $oldControllerPathsDataProvider;

    public function __construct(OldControllerPathsDataProvider $oldControllerPathsDataProvider)
    {
        $this->oldControllerPathsDataProvider = $oldControllerPathsDataProvider;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [String_::class];
    }

    /**
     * @param String_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $oldControllerFileNames = $this->oldControllerPathsDataProvider->provideFileNames();

        $stringValue = $this->getValue($node);
        if (! in_array($stringValue, $oldControllerFileNames, true)) {
            return null;
        }

        $matches = Strings::match($stringValue, '#(?<route_name>.*?)\.php$#');
        $routeName = $matches['route_name'] ?? '';

        return new String_($routeName);
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Replace .php like paths with routes');
    }
}
