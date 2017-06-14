#!/usr/bin/python
# -*- coding: utf-8 -*-

from __future__ import print_function
import csv
import os
import unicodedata
import mysql.connector 
import re

def treatMetamorphose(value):
  ret = value
  regex = "[A-Za-zé]+ \((.*\))[ ]+:[ ]?(.*)"
  m = re.search(regex, value)
  if m is not None:
    #print("grp1 "+m.group(1))
    #print("grp2 "+m.group(2))
    ret = m.group(1)+"|"+m.group(2)
  return ret

def treatPouvoir(value):
  index = value.index('»')
  formule = value[:index+2]
  effet = value[index+2:]
  return formule+"|"+effet


DEBUG = 0

f = open('Maitrises-2016.csv')
if f == None:
	print("Erreur a l'ouverture du fichier")
	exit(1)	


headers = None
i = 0
spamreader = csv.reader(f, delimiter='|', quotechar='"')

try:
	conn = mysql.connector.connect(host="localhost",user="root",password="my-secret-pw", database="Norlande")
except Exception as error:
	print(error)
	exit(1)
	
cursor = conn.cursor()
            

query="""UPDATE competences 
SET parcelles=%s, 
possessions_depart=%s, 
a_prevoir=%s,
connaissances=%s,
aide_jeu=%s,
maniement=%s,
attaque_spe=%s,
attaque_spe_tranchant=%s,
attaque_spe_contondant=%s,
attaque_spe_hast=%s,
attaque_spe_tir=%s,
attaque_spe_lancer=%s,
sortilege=%s,
sort_masse1=%s,
sort_masse2=%s,
immunite_etoffe=%s,
immunite_cuir=%s,
immunite_maille=%s,
immunite_plaque=%s,
amelioration=%s,
capacite=%s,
technique1=%s,
technique2=%s,
piege1=%s,
piege2=%s,
breuvage1=%s,
breuvage2=%s,
breuvage3=%s,
breuvage4=%s,
breuvage5=%s,
invocation1=%s,
invocation2=%s,
metamorphose=%s,
pouvoir1=%s,
pouvoir2=%s,
pouvoir3=%s,
pouvoir4=%s
WHERE (competence_nom=%s AND maitrise=%s)"""
           



for tab_line in spamreader:
	if i == 0:
		headers = tab_line
		i += 1
		continue
		
	maitrise_brut = tab_line[1]
	
	nom = tab_line[3]
	print(nom)
	
	parcelle = tab_line[5]
	possessions_depart = tab_line[6]
	a_prevoir = tab_line[7]
	connaissances = tab_line[9]
	aide_jeu = tab_line[10]
	maniement = tab_line[19]
	attaque_spe = tab_line[20]
	attaque_spe_tranchant = tab_line[21]
	attaque_spe_contondant = tab_line[22]
	attaque_spe_hast = tab_line[23]
	attaque_spe_tir = tab_line[24]
	attaque_spe_lancer = tab_line[25]
	sortilege = tab_line[26].replace("\n", "|")
	sort_masse1 = tab_line[27].replace("\n", "|")
	sort_masse2 = tab_line[28].replace("\n", "|")
	immunite_etoffe = tab_line[33]
	immunite_cuir = tab_line[36]
	immunite_maille = tab_line[39]
	immunite_plaque = tab_line[42]
	
	amelioration = tab_line[43]
	
	capacite = ""
	if tab_line[44] != "":
		capacite = tab_line[44] + "|" + tab_line[45]
	
	technique1 = ""
	if tab_line[46] != "":
		technique1 = tab_line[46] + "|" + tab_line[47] + "|" + tab_line[48]
		
	technique2 = ""
	if tab_line[49] != "":
		technique2 = tab_line[49] + "|" + tab_line[50] + "|" + tab_line[51]
		
	piege1 = ""
	if tab_line[52] != "":
		piege1 = tab_line[52] + "|" + tab_line[53] + "|" + tab_line[54]
	
	piege2 = ""
	if tab_line[55] != "":
		piege2 = tab_line[55] + "|" + tab_line[56] + "|" + tab_line[57]
	
	breuvage1 = ""
	if tab_line[58] != "":
		breuvage1 = tab_line[58] + "|" + tab_line[59] + "|" + tab_line[60]
		
	breuvage2 = ""
	if tab_line[61] != "":
		breuvage2 = tab_line[61] + "|" + tab_line[62] + "|" + tab_line[63]
		
	breuvage3 = ""
	if tab_line[64] != "":
		breuvage3 = tab_line[64] + "|" + tab_line[65] + "|" + tab_line[66]
		
	breuvage4 = ""
	if tab_line[67] != "":
		breuvage4 = tab_line[67] + "|" + tab_line[68] + "|" + tab_line[69]
		
	breuvage5 = ""
	if tab_line[70] != "":
		breuvage5 = tab_line[70] + "|" + tab_line[71] + "|" + tab_line[72]
		
	invocation1 = ""
	if tab_line[73] != "":
		invocation1 = tab_line[73] + "|" + tab_line[74] + " mana|" + tab_line[75]
		
	invocation2 = ""
	if tab_line[76] != "":
		invocation2 = tab_line[76] + "|" + tab_line[77] + " mana|" + tab_line[78]

	metamorphose = treatMetamorphose(tab_line[79])
	
	pouvoir1 = ""
	if tab_line[80] != "":
		pouvoir1 = tab_line[80] + "|" + treatPouvoir(tab_line[81])
	
	pouvoir2 = ""
	if tab_line[82] != "":
		pouvoir2 = tab_line[82] + "|" + treatPouvoir(tab_line[83])
		
	pouvoir3 = ""
	if tab_line[84] != "":
		pouvoir3 = tab_line[84] + "|" + treatPouvoir(tab_line[85])
		
	pouvoir4 = ""
	if tab_line[86] != "":
		pouvoir4 = tab_line[86] + "|" + treatPouvoir(tab_line[87])
	
	
	
	data = (parcelle, possessions_depart, a_prevoir, connaissances, aide_jeu, \
	maniement, attaque_spe, attaque_spe_tranchant, attaque_spe_contondant, \
	attaque_spe_hast, attaque_spe_tir, attaque_spe_lancer, sortilege, \
	sort_masse1, sort_masse2, immunite_etoffe, immunite_cuir, \
	immunite_maille, immunite_plaque, amelioration, capacite, \
	technique1, technique2, piege1, piege2, breuvage1, breuvage2, \
	breuvage3, breuvage4, breuvage5, invocation1, invocation2, \
	metamorphose, pouvoir1, pouvoir2, pouvoir3, pouvoir4, nom, maitrise_brut)
	
	
	if DEBUG == 1:
		print("")
		if capacite != "":
			print(capacite)			
			
		if technique1 != "":
			print(technique1)		
		
		if piege1 != "":
			print(piege1)	
		
		if breuvage1 != "":
			print(breuvage1)	
		
		if pouvoir1 != "":
			print(pouvoir1)

			
	try:
		cursor = conn.cursor()
		cursor.execute(query, data)
		conn.commit()
	except Exception as error:
		print(cursor.statement)
		#print(data)
		
		print(error)
		exit(1)
	finally:
		cursor.close()
		
conn.close()

	
