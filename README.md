# Phodam

Phodam is inspired by [PODAM](https://mtedone.github.io/podam/).

Phodam (pronounced Faux-dam) is a PHP library used to generate objects for unit tests. The main feature of PODAM is that you can give it a class and it generates a populated Object with all fields populated.

Phodam, in its current state, will populate objects as long as it's given a `TypeProviderInterface` for a specific class.

## Usage
```php
class SportsTeamProvider implements TypeProviderInterface
{
    public function create(array $overrides = [])
    {
        $team = new SportsTeam();
        $team->setLocation('New Jersey');
        $team->setTeamName('Devils');
        $team->setLeague('NHL');
        $team->setFoundedIn(1982);
        return $team;
    }
}

// in your test case base class
$this->phodam = new Phodam();

$sportsTeamProvider = new SportsTeamProvider();
$sportsTeamProviderConfig = new \Phodam\Provider\TypeProviderConfig($sportsTeamProvider);
$sportsTeamProviderConfig->forClass(SportsTeam::class);
$sportsTeamProviderConfig->withName('Hockey');

$this->phodam->registerTypeProviderConfig($sportsTeamProviderConfig);

$team = $this->phodam->create(SportsTeam::class, 'Hockey');
// $team is now an object with a randomly generated location and name
// 'NHL' for the league, and a random 'founded' year between 1920 and this year
```

The idea is that you should be able to specify a way to create a generic `SportsTeam` or a specific type of `SportsTeam` that can then be used from Phodam.

## Local Build

```sh
./quickBuild.sh
```
