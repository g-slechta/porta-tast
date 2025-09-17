<?php

namespace App\Command;

use App\Config\ProjectFileSystem;
use App\Transformer\UserArrayTransform;
use SplFileObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\File;

class UserTransformCommand extends Command
{
    public function __construct(
        protected ProjectFileSystem $projectFileSystem
    )
    {
        parent::__construct('user:transform');
    }

    public function transformUserData(string $sourceFilePath, string $targetFilePath, mixed $hashPassword, SymfonyStyle $io): void
    {
        $transformer = new UserArrayTransform();
        $sourceFile = new File($sourceFilePath);
        $csv = $sourceFile->openFile();
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
        $targetFile = (new File($targetFilePath, false))->openFile('w');

        $head = $csv->current();
        $userValueCount = count($head);
        $csv->next();

        $targetFile->fwrite('[');
        $separator = '';
        while ($csv->valid() && is_array($csv->current())) {
            try {
                if ($userValueCount != count($csv->current())) {
                    throw new \InvalidArgumentException("line data is corrupted");
                }
                $userData = $transformer->transform(array_combine($head, $csv->current()), $hashPassword);
                $targetFile->fwrite($separator . json_encode($userData));
                $separator = ',';
            } catch (\Exception $e) {
                $io->error("Corrupted row [{$csv->key()}]:" . $e->getMessage());
            }
            $csv->next();
        }
        $targetFile->fwrite(']');
    }

    protected function configure(): void
    {
        $this->setDescription('Transform a user from csv to json file')
        ->addOption('hash-password','p',InputOption::VALUE_NONE, 'hashing plaintext password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Transform a user from csv to json file');

        $hashPassword = $input->getOption('hash-password');
        do{
            $sourceFilePath = Path::makeAbsolute(
                $io->ask(
                    'source file',
                    'var/tmp/data_ukol.csv'
                ), $this->projectFileSystem->getRootDir()
            );
        }while(!$this->projectFileSystem->exists($sourceFilePath));

        $targetFilePath = Path::makeAbsolute(
            $io->ask(
            'target file in '.$this->projectFileSystem->getTmpDir(),
            'data_ukol.json'
            ), $this->projectFileSystem->getTmpDir()
        );
        $targetFilePath = Path::makeRelative($targetFilePath, $this->projectFileSystem->getRootDir());
        $this->projectFileSystem->getDir(... explode(DIRECTORY_SEPARATOR, dirname($targetFilePath)));

        $this->transformUserData($sourceFilePath, $targetFilePath, $hashPassword, $io);

        return Command::SUCCESS;
    }
}