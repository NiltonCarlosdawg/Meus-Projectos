import Hero from "../components/dash/Hero"
import Nav from "../components/header/Nav"

const Home = () => {
  return (
    <div className="bg-[#00b264] h-[300vh]">
      <Nav/>
      <Hero/>
    </div>
  )
}

export default Home