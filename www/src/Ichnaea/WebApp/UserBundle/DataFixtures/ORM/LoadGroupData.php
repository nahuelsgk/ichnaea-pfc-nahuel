<?php 

namespace Ichnaea\WebApp\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ichnaea\WebApp\UserBundle\Entity\Group;
use Ichnaea\WebApp\UserBundle\Entity\User;
use Ichnaea\WebApp\MatrixBundle\Entity\Variable;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSet;
use Ichnaea\WebApp\MatrixBundle\Entity\SeasonSetComponent;
use Ichnaea\WebApp\MatrixBundle\Entity\Season;

class LoadGroupData implements FixtureInterface, ContainerAwareInterface 
{
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * {@inheritDoc}
	 */
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		//First enrol all existing users to a group
		$userManager = $this->container->get('fos_user.user_manager');
		$admin_user = $userManager->findUserBy(array('username'=>'admin'));
		
		//This group will be used later
		$group_user = '';
		if (sizeof($admin_user)) echo "Admin already exists\n";
		else {
			echo "Admin do not exists. We will create admin user and two groups";
			$user = new User();
			$user->setUsername('admin');
			$user->setPlainPassword('ichnaea');
			$user->setEmail('noemail@mail.com');
			$user->setEnabled(true);
		
			//Administrators
			$group_admins = new Group('administrator');
			$group_admins = $group_admins->setRoles(array('ROLE_SUPER_ADMIN', 'ROLE_USER'));
			$user->addGroup($group_admins);
			$manager->persist($group_admins);
			$manager->flush();
			
		
			//Users
			$group_users= new Group('user');
			$group_users->setRoles(array('ROLE_USER'));
			$group_user = $group_users;
			$manager->persist($group_users);
			$manager->flush();
			
			$manager->persist($user);
			$manager->flush();
			
		}
		
		//Enroll all user that has no group, as normal users 
		$users = $userManager->findUsers();
		foreach ($users as $user)
		{
			$groups = $user->getGroups();
			if (sizeof($groups) == 0){
				$user->addGroup($group_user);
				$userManager->updateUser($user);
			}
		}
		
		
		$variable = array(
		'FC'        => $this->build_variable('FC','envFC-Estiu.txt', 'envFC-Hivern.txt'),
		'FE'        => $this->build_variable('FE','envFE-Estiu.txt', 'envFE-Hivern.txt'),
		'CL'        => $this->build_variable('CL','envCL-Estiu.txt', 'envCL-Hivern.txt'),
		'SOMCPH'    => $this->build_variable('SOMCPH','envSOMCPH-Estiu.txt', 'envSOMCPH-Hivern.txt'), 
		'FRNAPH'    => $this->build_variable('FRNAPH','envFRNAPH-Estiu.txt', 'envFRNAPH-Hivern.txt'), 
		'FRNAPH.I'  => $this->build_variable('FRNAPH.I','envFRNAPH.I-Estiu.txt', 'envFRNAPH.I-Hivern.txt'), 
		'FRNAPH.II' => $this->build_variable('FRNAPH.II','envFRNAPH.II-Estiu.txt', 'envFRNAPH.II-Hivern.txt'), 
		'FRNAPH.III'=> $this->build_variable('FRNAPH.III','envFRNAPH.III-Estiu.txt', 'envFRNAPH.III-Hivern.txt'), 
		'FRNAPH.IV' => $this->build_variable('FRNAPH.IV','envFRNAPH.IV-Estiu.txt', 'envFRNAPH.IV-Hivern.txt'), 
		'RYC2056'   => $this->build_variable('RYC2056','envRYC2056-Estiu.txt', 'envRYC2056-Hivern.txt'),
		'DiE'       => $this->build_variable('DiE','envDiE-Estiu.txt', 'envDiE-Hivern.txt'),
		'FM-FS'     => $this->build_variable('FM-FS','envFMFS-Estiu.txt', 'envFMFS-Hivern.txt'),
		'Hir'       => $this->build_variable('Hir','envHiR-Estiu.txt', 'envHiR-Hivern.txt'),
		'DiC'       => $this->build_variable('DiC','envDiC-Estiu.txt', 'envDiC-Hivern.txt'),
		'ECP'       => $this->build_variable('ECP','envECP-Estiu.txt', 'envECP-Hivern.txt'),
		'GA17'      => $this->build_variable('GA17','envGA17-Estiu.txt', 'envGA17-Hivern.txt'),
		'HBSA.Y'    => $this->build_variable('HBSA.Y','envHBSA.Y-Estiu.txt', 'envHBSA.Y-Hivern.txt'),
		'HBSA.T'    => $this->build_variable('HBSA.T','envHBSA.T-Estiu.txt', 'envHBSA.T-Hivern.txt'),
		);
		
		foreach ($variable as $k => $v){
			$variable = new Variable();
			$variable->setName($k);
			$variable->setDescription($k);
			$manager->persist($variable);
			
			$seasonSet = new SeasonSet();
			$seasonSet->setName($v['season_set_name']);
			$seasonSet->setVariable($variable);
			$manager->persist($seasonSet);
			
			$season_a = new Season();
			$season_a->setName($v['summer']);
			$season_a->setContent($this->read_file($v['summer']));
			$manager->persist($season_a);
			
			$seasonSetComponent = new SeasonSetComponent();
			$seasonSetComponent->setSeason($season_a);
			$seasonSetComponent->setSeasonSet($seasonSet);
			$seasonSetComponent->setSeasonType('summer');
			$manager->persist($seasonSetComponent);
			
			$season_b = new Season();
			$season_b->setName($v['winter']);
			$season_b->setContent($this->read_file($v['winter']));
			$manager->persist($season_b);
			
			$seasonSetComponent = new SeasonSetComponent();
			$seasonSetComponent->setSeason($season_b);
			$seasonSetComponent->setSeasonSet($seasonSet);
			$seasonSetComponent->setSeasonType('winter');
			$manager->persist($seasonSetComponent);
			$manager->flush();
		}
		
		$serviceMatrix = $this->container->get('ichnaea.service');
		$content = $this->read_matrix_file();
		$serviceMatrix->createMatrixFromCSVContent('cyprus',$content, $user->getId());
		
		//@TODO: create all cases in the csv
	}
	
	private function build_variable($name, $summer_file, $winter_file)
	{
		return array(
			"season_set_name" => "$name default season set",
			"summer" => $summer_file,
			"winter" => $winter_file
		);
	}
	
	private function read_matrix_file()
	{
		return file_get_contents($this->container->get('kernel')->getRootDir().'/../../r/fixtures/cyprus.csv');
	}
	
	private function read_file($file_name)
	{
		
		$content = file_get_contents($this->container->get('kernel')->getRootDir().'/../../r/fixtures/aging/'.$file_name);
		return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
	}
}