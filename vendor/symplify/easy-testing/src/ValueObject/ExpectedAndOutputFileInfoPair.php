<?php

declare (strict_types=1);
namespace RevealPrefix20220711\Symplify\EasyTesting\ValueObject;

use RevealPrefix20220711\Symplify\SmartFileSystem\SmartFileInfo;
use RevealPrefix20220711\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class ExpectedAndOutputFileInfoPair
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $expectedFileInfo;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $outputFileInfo;
    public function __construct(SmartFileInfo $expectedFileInfo, ?SmartFileInfo $outputFileInfo)
    {
        $this->expectedFileInfo = $expectedFileInfo;
        $this->outputFileInfo = $outputFileInfo;
    }
    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getExpectedFileContent() : string
    {
        return $this->expectedFileInfo->getContents();
    }
    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getOutputFileContent() : string
    {
        if (!$this->outputFileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }
        return $this->outputFileInfo->getContents();
    }
    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function doesOutputFileExist() : bool
    {
        return $this->outputFileInfo !== null;
    }
}
