RASPMONITOR
========================

RASPMONITOR is simple website monitor that checks if a group of webservers are working.
It's intended to use with a raspberry pi computer, but you can install in any
computer compatible with PHP.

This project is base on the Symfony2 Standard Edition 2.3 (LTS).

For information about installing Symfony2, you can find more detailed info
at http://symfony.com/doc/current/book/installation.html

You will need al latest PHP 5.3.3. and a database driver. Raspmonitor is compatible
 with doctrin2e databaes (SQLITE, MYSQL, PgSQL or Oracle).


1) Installing Raspmonitor
----------------------------------

Clone the repository:

    git clone git@github.com:xmontana/raspmonitor.git

Check Symfony2 compatibility with your sistem:

    php app/check.php

Download/install Composer (https://getcomposer.org/download/)

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

At the end of the installation script you will ask for the raspmonitor
config parameters (database, email account for sending alerts...)

2) Installing database
----------------------------------
Execute:

    php app/console doctrine:database:create
    php app/console doctrine:schema:create

If you want a default content (will erase the database):

    php app/console doctrine:fixtures:load


4) Add a frontend user
----------------------------------

If you have loaded the database fixtures, your default user is
"admin" with password "raspadmin". If you need to create more users, you can use
 the console command of the FOS bundle:

    php app/console fos:create:user

    php app/console fos:create:activate

5) Directory write permisions
----------------------------------

In RASPBIAN distribution, the deafult filesistem doesn't have installed ACL, so if
 the command "setacl" doesn't work, type:

    sudo apt-get install acl

And in /etc/fstab add the option "acl" to the root file system:

    /etc/fstab:  /dev/mmcblk0p2  /               ext4    defaults,noatime,acl   0       1


For seting the correct filesystem write permissions (apache user and console user) type:

    setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs app/var
    sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs app/var


6) CRON
-----------------------------------

You will need to configure 2 cron tasks, one for the check, and other one to purge the logs
older than 1 week (raspberry will be very slow to access LOG table with 1 week of recors):


    * * * * * /var/www/vhosts/raspmonitor/app/console raspberry:monitor:check -q
    0 0 * * * /var/www/vhosts/raspmonitor/app/console raspberry:monitor:purge -q

You can test the command check with the "verbose" flag to see checks status from prompt:

    var/www/vhosts/raspmonitor/app/console raspberry:monitor:check -v


7) UPGRADING
------------------------------------

Firts check last GIT version:

     git pull


Update the components:

     php composer.phar install

Check if there's any database changes:

     php app/console doctrine:schema:update --force

Clear Symfony2 Cache:

     php app/console cache:clear --env=prod


8) LICENSE
------------------------------------

RASPMONITOR is licensed to Xavier Montaña Carreras (xmontana[AT]gmail.com)
under a Creative Commons Attribution 4.0 International License.

You can:

  Share — copy and redistribute the material in any medium or format
  Adapt — remix, transform, and build upon the material
  for any purpose, even commercially.
  The licensor cannot revoke these freedoms as long as you follow the license terms.

Under the following terms:

  Attribution — You must give appropriate credit, provide a link to the license, and indicate if changes were made.

Symfony2 is released under the MIT license. Copyright (c) 2004-2014 Fabien Potencier

Other Symfony2 Bundles used under the owner's LICENSE:

   friendsofsymfony/user-bundle
   ronanguilloux/php-gpio
   misd/guzzle-bundle
   apy/datagrid-bundle


THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.