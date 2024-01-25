<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';

set('bin/php', 'php7.4');

// Project name
set('application', 'archivni-fondy-moravske-galerie');

// Project repository
set('repository', 'https://github.com/SlovakNationalGallery/archivni-fondy-moravske-galerie.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', [
]);
add('shared_dirs', [
    'resources/fonts',
]);

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('lab_sng@webumenia.sk')
    ->set('deploy_path', '/var/www/afmg.webumenia.sk')
    ->set('user', 'lab_sng');

// Tasks

task('build', function () {
    run('cd {{release_path}} && {{bin/npm}} run production');
});

task('elastic:migrate', artisan('elastic:migrate --force', ['showOutput']));

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');
before('deploy:symlink', 'elastic:migrate');

after('deploy:update_code', 'npm:install');
after('deploy:shared', 'build');
