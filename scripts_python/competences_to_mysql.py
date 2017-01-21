#!/usr/bin/python
# -*- coding: utf-8 -*-

from __future__ import print_function
import csv
import os
import unicodedata
import mysql.connector 
import re


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

##
# SET pieces_or = %d,
# SET pieces_argent = %d,
# SET pieces_cuivre = %d,
            

query="""UPDATE competences 
SET niveau_langue=%s, 
lecture_ecriture=%s, 
rumeurs=%s,
actions_guerre=%s,
coup_force=%s,
voix_noires=%s,
voix_blanches=%s,
voix_peuple=%s,
voix_roi=%s,
veto=%s,
manigance=%s,
lieux_pouvoir=%s,
force_physique=%s,
bonus_mana=%s,
globes_sortilege=%s,
bonus_coups=%s,
bonus_coup_etoffe=%s,
esquive_etoffe=%s,
resiste_etoffe=%s,
esquive_cuir=%s,
resiste_cuir=%s,
esquive_maille=%s,
resiste_maille=%s,
esquive_plaque=%s,
resiste_plaque=%s
WHERE competence_nom=%s"""
                
                
def lecture_ecriture_to_bool(val):
	if val == None or val == "":
		return 0
	return 1
	
def get_actions_cours(val):
	manigance = 0
	coup_force = 0
	voix_noires = 0
	voix_blanches = 0
	voix_peuple = 0
	voix_roi = 0
	veto = 0
	
	m = re.search('([0-9]{1,2})[ a-z]{1,8} noire', val, re.IGNORECASE)
	if m is not None:
		voix_noires = int(m.group(1))
		
	m = re.search('([0-9]{1,2})[ a-z]{1,8} blanc', val, re.IGNORECASE)
	if m is not None:
		voix_blanches = int(m.group(1))
		
	m = re.search('([0-9]{1,2})[ a-z]{1,8} peupl', val, re.IGNORECASE)
	if m is not None:
		voix_peuple = int(m.group(1))
		
	m = re.search('([0-9]{1,2})[ a-z]{1,8} roi', val, re.IGNORECASE)
	if m is not None:
		voix_roi = int(m.group(1))
		
	m = re.search('([0-9]{1,2}).manigan', val, re.IGNORECASE)
	if m is not None:
		manigance = int(m.group(1))
		
	m = re.search('([0-9]{1,2}).veto', val, re.IGNORECASE)
	if m is not None:
		veto = int(m.group(1))
		
	m = re.search('([0-9]{1,2})[ a-z]{1,9} force', val, re.IGNORECASE)
	if m is not None:
		coup_force = int(m.group(1))
	
	return manigance, coup_force, voix_noires, voix_blanches, voix_peuple, voix_roi, veto
	
def get_action_guerre(val):
	coup_force = 0
	return coup_force
	
def get_lieu_pouvoir(val):
	lieu = ""
	m = re.search('guerre', val, re.IGNORECASE)
	if m is not None:
		lieu = "Table de la Guerre"
	m = re.search('conseil', val, re.IGNORECASE)
	if m is not None:
		lieu = "Table du Conseil"
	return lieu



def get_int(val):
	if val == '':
		return 0
	else:
		return int(val)

for tab_line in spamreader:
	if i == 0:
		headers = tab_line
		i += 1
		continue
		
	maitrise_brut = tab_line[1]
	
	nom = tab_line[3]
	print(nom)
	
	niveau_langue = tab_line[8]
	lecture_ecriture = lecture_ecriture_to_bool(tab_line[11]) # dans le excel : "Vous savez lire et écrire"... à transformer en true/false
	rumeurs = get_int(tab_line[12])
	action_guerre = get_int(tab_line[13])
	manigance, coup_force, voix_noires, voix_blanches, voix_peuple, voix_roi, veto = get_actions_cours(tab_line[14])
	lieux_pouvoir = tab_line[15]
	force_physique = get_int(tab_line[16])
	bonus_mana = get_int(tab_line[17])
	globes_sortilege = get_int(tab_line[18])
	bonus_coups = get_int(tab_line[29])
	bonus_coups_etoffe = get_int(tab_line[30])
	esquive_etoffe = get_int(tab_line[31])
	resiste_etoffe = get_int(tab_line[32])
	esquive_cuir = get_int(tab_line[34])
	resiste_cuir = get_int(tab_line[35])
	esquive_maille = get_int(tab_line[37])
	resiste_maille = get_int(tab_line[38])
	esquive_plaque = get_int(tab_line[40])
	resiste_plaque = get_int(tab_line[41])
	
	
	data = (niveau_langue, lecture_ecriture, rumeurs, action_guerre, coup_force, \
		voix_noires, voix_blanches, voix_peuple, voix_roi, veto, manigance, lieux_pouvoir, \
		force_physique, bonus_mana, globes_sortilege, bonus_coups, bonus_coups_etoffe, \
		esquive_etoffe, resiste_etoffe, esquive_cuir, resiste_cuir, esquive_maille, \
		resiste_maille, esquive_plaque, resiste_plaque, nom)
	
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
	
	

	
	
	
	
