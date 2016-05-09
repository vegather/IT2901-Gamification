<?php
	//Tips array which contains all the tips for the system.
	$tips = array(
	"Heating or cooling the whole house can be expensive. Where possible, shut doors to areas you are not using and only heat or cool the rooms you spend the most time in.",
	"In winter, heating can account for over 30% of your bill. Set your thermostat between 18 and 20 degrees. Every degree above 20 can add 10% to your heating bill. In summer, set your thermostat to 26 degrees or above.",
	"Turn off heating when you leave the room, or go to bed. With some ducted heating systems you can turn off the heating in the rooms that are unoccupied. Make sure all your heating or cooling is turned off when you leave the house.",
	"You can save around $115 per year by washing clothes in cold water. You can also save by making sure you select the shortest appropriate washing cycle and waiting until you have a full load.",
	"Your fridge is always on, making it one of your most expensive appliances. Make sure the door seal is tight and free from gaps so cold air can't escape.",
	"Did you know your phone charger is still using energy even when your phone is not attached? Up to 10% of your electricity could be used by gadgets and appliances that are on standby.",
	"Replace old incandescent and halogen light globes with energy-efficient globes. Energy-efficient globes save power and last longer. Light globes can sometimes be replaced for free or at reduced cost.",
	"When you are cooking, use the microwave when you can – it uses much less energy than an electric oven. If you use the stove, keep lids on your pots to reduce cooking time."
	);
	
	//Checks if household_id has been set
	if (isset($_GET["household_id"])) {
		$household_id = $_GET["household_id"];
		//Echo a random tip from the tips array
		echo $tips[rand(0, count($tips))];
	} else {
		echo "household_id must be set in order to retrieve tips!";
	}
?>