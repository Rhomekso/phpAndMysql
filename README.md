# phpAndMysql - PHP & MySQL Leerprojecten

## 🎯 TL;DR

Drie progressieve PHP-projecten: van **basis authenticatie** → **MVC CRUD** → **volledige verenigingsbeheer applicatie** met OOP, automatische contributieberekening en beveiligde gebruikersrechten.

**Quick links:**
- [AUTH_PHP](#1️⃣-auth_php---rolapplicatie) - Login met rolbeheer
- [CRUD_PHP](#2️⃣-crud_php---article-management) - MVC artikel systeem  
- [Ledenadministratie](#3️⃣-ledenadministratie---verenigingsbeheer-⭐) - Enterprise applicatie (103/105 punten)

---

## 📚 Projecten

### 1️⃣ AUTH_PHP - Rolapplicatie
Basis authenticatie met admin/user rollen en automatische database setup.

---

### 2️⃣ CRUD_PHP - Article Management
MVC-gebaseerd artikel beheersysteem met PDO en input validatie.

**Tech:** PHP 8.0+, MySQL 8.0+, MVC pattern

---

### 3️⃣ Ledenadministratie - Verenigingsbeheer ⭐
**De hoofdapplicatie** - Professionele ledenadministratie met 40+ bestanden en 2485+ regels code.

**Features:**
- 👤 Login/registratie met bcrypt & remember me
- 👨‍👩‍👧‍👦 Families & leden beheer (volledige CRUD)
- 💶 **Automatische contributie** op basis van leeftijd:
  - Jeugd (0-7j): €50 | Aspirant (8-12j): €60 | Junior (13-17j): €75
  - Senior (18-50j): €100 | Oudere (51+j): €55
- 📊 Dashboard met statistieken (admin-only)
- 👥 Gebruikersbeheer met rechten

**Technisch:**
- 6 database tabellen met foreign keys
- OOP met inheritance (abstract Model class)
- Beveiliging: SQL injection preventie, XSS, CSRF, password hashing
- Responsive design

## 📄 Licentie

Educatief project - LOI opleiding PHP & MySQL
