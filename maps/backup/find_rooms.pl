#!/usr/bin/perl
use strict;
use warnings;
use Cwd;

#This program reads a xyz file then each atom kind of atom is numeroted corresponding to its line number in the xyz file. Then the script goes to the ANO-RCC file to read basis sets and the level of contraction given as parameter.


#input parameters :
#The first one is the name of the xyz file.
#The second one is the contraction level, if it is omitted, an error will occur.



#Opening of the input files : AOR-RCC and the xyz file
	open(my $input,  "<",  "M6Rdc.svg")  or die "Can't open input: $!";
	#ouverture du fichier de sortie
	open(my $out, ">",  "M6Rdc.csv") or die "Can't open output.txt: $!";

my $test=0;

#Reading of the xyz file the first two lines are ignored
while (<$input>) 
{ 
	if(/id="(M6(.*))"$/)
	{
		print $out "M6,Rdc,".$1.",".$2."\n";
	}

}

